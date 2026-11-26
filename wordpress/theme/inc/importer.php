<?php
/**
 * Outils › Importer le site WI
 *
 * One click creates the pages, the « Missions & Éducation » items and the
 * « Talents » with all the texts and photos of the current site, and sets the
 * home page. With Polylang and an English language, it can also create linked
 * English copies (French text to translate).
 */

defined( 'ABSPATH' ) || exit;

const WI_IMPORT_OPTION = 'wi_import_state';

add_action( 'admin_menu', function () {
	add_management_page( 'Importer le site WI', 'Importer le site WI', 'manage_options', 'wi-import', 'wi_import_screen' );
} );

function wi_import_screen() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$state    = get_option( WI_IMPORT_OPTION, array() );
	$has_scf  = function_exists( 'update_field' );
	$has_pll  = function_exists( 'pll_languages_list' );
	$langs    = $has_pll ? pll_languages_list() : array();
	$default  = $has_pll ? pll_default_language() : '';
	$log      = array();

	if ( isset( $_POST['wi_import'] ) && check_admin_referer( 'wi_import' ) && $has_scf ) {
		if ( ! empty( $state['ids'] ) && empty( $_POST['wi_replace'] ) ) {
			$log[] = 'Le contenu a déjà été importé. Cochez « Supprimer et réimporter » pour recommencer.';
		} else {
			$log = wi_import_run( ! empty( $_POST['wi_en'] ) );
			$state = get_option( WI_IMPORT_OPTION, array() );
		}
	}
	?>
	<div class="wrap">
		<h1>Importer le site Wildlife Interactions</h1>
		<p>Crée les pages (Accueil, Missions, Éducation, Talents, Exposition &amp; média, Contact), les missions et programmes, les talents, avec tous les textes et photos du site actuel. Ensuite, tout se modifie dans l’administration.</p>

		<?php if ( ! $has_scf ) : ?>
			<div class="notice notice-error inline"><p>Installez et activez d’abord l’extension <strong>Secure Custom Fields</strong> (Extensions › Ajouter).</p></div>
		<?php endif; ?>
		<?php if ( $has_pll && ! $langs ) : ?>
			<div class="notice notice-warning inline"><p>Polylang est actif mais aucune langue n’est créée : ajoutez d’abord Français (et English) dans Langues.</p></div>
		<?php endif; ?>

		<?php if ( $log ) : ?>
			<div class="notice notice-info inline"><ul style="list-style:disc;padding-left:20px"><?php foreach ( $log as $line ) : ?><li><?php echo esc_html( $line ); ?></li><?php endforeach; ?></ul></div>
			<p><a class="button button-primary" href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank">Voir le site</a></p>
		<?php endif; ?>

		<form method="post">
			<?php wp_nonce_field( 'wi_import' ); ?>
			<?php if ( $has_pll && in_array( 'en', $langs, true ) && 'en' !== $default ) : ?>
				<p><label><input type="checkbox" name="wi_en" value="1" checked> Créer aussi la version anglaise (copie liée du contenu français, à traduire ensuite)</label></p>
			<?php endif; ?>
			<?php if ( ! empty( $state['ids'] ) ) : ?>
				<p><label><input type="checkbox" name="wi_replace" value="1"> Supprimer le contenu importé précédemment et réimporter (les modifications faites depuis seront perdues)</label></p>
			<?php endif; ?>
			<p><button class="button button-primary button-hero" name="wi_import" value="1" <?php disabled( ! $has_scf ); ?>>Importer le contenu</button></p>
			<p class="description">Prend environ une minute (import des photos dans la Médiathèque).</p>
		</form>
	</div>
	<?php
}

/* --------------------------------------------------------------------------- */

function wi_import_run( $with_en ) {
	@set_time_limit( 600 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	// The PolarX logo is an SVG bundled with the theme (trusted file): allow SVG for this import only.
	$allow_svg = function ( $mimes ) {
		$mimes['svg'] = 'image/svg+xml';
		return $mimes;
	};
	add_filter( 'upload_mimes', $allow_svg );

	$log   = array();
	$state = get_option( WI_IMPORT_OPTION, array() );

	// Replace: remove the posts created last time (photos stay in the Media Library and are reused).
	if ( ! empty( $state['ids'] ) ) {
		foreach ( $state['ids'] as $id ) {
			wp_delete_post( (int) $id, true );
		}
		$log[] = count( $state['ids'] ) . ' éléments importés précédemment supprimés.';
	}
	$state['ids'] = array();
	update_option( WI_IMPORT_OPTION, $state, false );

	$json = json_decode( file_get_contents( WI_DIR . '/seed/content.json' ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	$pages = require WI_DIR . '/seed/pages.php';
	if ( ! $json ) {
		return array( 'Fichier seed/content.json illisible.' );
	}

	$pll   = function_exists( 'pll_set_post_language' ) && function_exists( 'pll_default_language' ) && pll_default_language();
	$langs = $pll ? array( pll_default_language() ) : array( '' );
	if ( $pll && $with_en && 'en' !== pll_default_language() && in_array( 'en', pll_languages_list(), true ) ) {
		$langs[] = 'en';
	}

	$created = array(); // [ seed key ][ lang ] = post ID
	foreach ( $langs as $lang ) {
		$created = array_merge_recursive( $created, wi_import_language( $lang, $json, $pages ) );
	}

	if ( $pll && count( $langs ) > 1 ) {
		foreach ( $created as $per_lang ) {
			if ( count( $per_lang ) > 1 ) {
				pll_save_post_translations( $per_lang );
			}
		}
	}

	// same key as in wi_import_language(): the language slug, or 'default' without Polylang
	$main  = $langs[0] ? $langs[0] : 'default';
	$front = isset( $created['page:home'][ $main ] ) ? $created['page:home'][ $main ] : 0;
	if ( $front ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $front );
	}
	if ( ! get_option( 'permalink_structure' ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
	}
	flush_rewrite_rules();
	remove_filter( 'upload_mimes', $allow_svg );

	$count = array( 'page' => 0, 'zone' => 0, 'talent' => 0 );
	foreach ( array_keys( $created ) as $key ) {
		$count[ strtok( $key, ':' ) ] += count( $created[ $key ] );
	}
	$log[] = sprintf( 'Importé : %d pages, %d missions / programmes, %d talents%s.', $count['page'], $count['zone'], $count['talent'], count( $langs ) > 1 ? ' (français + copies anglaises)' : '' );
	$log[] = 'Page d’accueil définie : « Accueil ».';
	if ( count( $langs ) > 1 ) {
		$log[] = 'Version anglaise : les textes sont en français, à traduire dans chaque contenu « EN ». Les libellés fixes (boutons, menus) se traduisent dans Langues › Traductions.';
	}
	return $log;
}

/** Create everything in one language. Returns [ seed key => [ lang => ID ] ]. */
function wi_import_language( $lang, $json, $pages ) {
	$out = array();
	$k   = $lang ? $lang : 'default';

	// Talents
	$talent_ids = array(); // normalized name => ID
	$by_photo   = array(); // photo key => ID
	foreach ( $json['talents'] as $i => $t ) {
		$id = wi_import_post( 'wi_talent', $t['name'], sanitize_title( $t['name'] ), $i, $lang );
		if ( ! empty( $t['photo'] ) ) {
			$photo = wi_import_image( 'assets/img/talents/' . $t['photo'] . '.jpg', $t['name'] );
			if ( $photo ) {
				set_post_thumbnail( $id, $photo );
			}
			$by_photo[ $t['photo'] ] = $id;
		}
		wi_import_fields( $id, 'talent', array(
			'role'     => $t['role'],
			'bio'      => $t['bio'],
			'linkedin' => isset( $t['linkedin'] ) ? $t['linkedin'] : '',
		) );
		$talent_ids[ wi_import_norm( $t['name'] ) ] = $id;
		$out[ 'talent:' . sanitize_title( $t['name'] ) ][ $k ] = $id;
	}

	// Missions & Éducation
	foreach ( $json['zones'] as $i => $z ) {
		$id   = wi_import_post( 'wi_zone', $z['name'], $z['slug'], $i, $lang );
		$d    = $z['detail'];
		$more = isset( $json['detailMore'][ $z['slug'] ] ) ? $json['detailMore'][ $z['slug'] ] : array();

		$team = array();
		if ( ! empty( $more['team']['people'] ) ) {
			foreach ( $more['team']['people'] as $person ) {
				list( $name, $role ) = $person;
				$norm = wi_import_norm( $name );
				if ( ! isset( $talent_ids[ $norm ] ) ) { // in a team but not on the Talents page: draft profile (shown in the team only)
					$talent_ids[ $norm ] = wi_import_post( 'wi_talent', $name, sanitize_title( $name ), 100 + count( $talent_ids ), $lang, 'draft' );
					$out[ 'talent:' . sanitize_title( $name ) ][ $k ] = $talent_ids[ $norm ];
				}
				$team[] = array( 'talent' => $talent_ids[ $norm ], 'role' => $role );
			}
		}

		$place = null;
		foreach ( $json['places'] as $p ) {
			if ( in_array( $z['slug'], $p['slugs'], true ) ) {
				$place = $p;
				break;
			}
		}

		wi_import_fields( $id, 'zone', array(
			'category'     => $z['category'],
			'summary'      => $z['summary'],
			'image'        => $z['image'],
			'bg'           => $z['bg'] !== $z['image'] ? $z['bg'] : '',
			'bg_opacity'   => $z['bgOpacity'],
			'lead'         => $d['lead'],
			'enjeux_title' => isset( $d['enjeuxTitle'] ) ? $d['enjeuxTitle'] : '',
			'enjeux_image' => $d['enjeuxImage'],
			'enjeux'       => $d['enjeux'],
			'stats'        => $d['stats'],
			'sections'     => isset( $more['sections'] ) ? $more['sections'] : array(),
			'more_stats'   => isset( $more['stats'] ) ? $more['stats'] : array(),
			'team_tag'     => isset( $more['team']['tag'] ) ? $more['team']['tag'] : '',
			'team_title'   => isset( $more['team']['title'] ) ? $more['team']['title'] : '',
			'team'         => $team,
			'place'        => $place ? $place['name'] : '',
			'lat'          => $place ? $place['lat'] : '',
			'lon'          => $place ? $place['lon'] : '',
			'stem'         => $place ? $place['stem'] : 48,
			'side'         => $place && isset( $place['side'] ) ? $place['side'] : 'right',
		) );
		$out[ 'zone:' . $z['slug'] ][ $k ] = $id;
	}

	// Pages
	foreach ( $pages as $key => $page ) {
		$slug = $lang && 'en' === $lang && ! empty( $page['slug_en'] ) ? $page['slug_en'] : $page['slug'];
		$id   = wi_import_post( 'page', $page['title'], $slug, $page['order'], $lang );
		if ( ! empty( $page['template'] ) ) {
			update_post_meta( $id, '_wp_page_template', $page['template'] );
		}
		$fields = $page['fields'];
		if ( 'home' === $key ) {
			$fields['faq_bg']         = $json['faq']['bg'];
			$fields['faq_bg_opacity'] = $json['faq']['bgOpacity'];
			$fields['faq']            = $json['faq']['items'];
		}
		if ( 'talents' === $key ) {
			$fields['categories'] = array();
			foreach ( $json['talentCategories'] as $c ) {
				$fields['categories'][] = array(
					'label'  => $c['label'] ? $c['label'] : '',
					'talent' => isset( $by_photo[ $c['photo'] ] ) ? $by_photo[ $c['photo'] ] : '',
					'alt'    => isset( $c['alt'] ) ? $c['alt'] : '',
				);
			}
		}
		$group = array( 'home' => 'home', 'missions' => 'zones_page', 'education' => 'zones_page', 'talents' => 'talents_page', 'expo' => 'expo', 'contact' => 'contact' )[ $key ];
		wi_import_fields( $id, $group, $fields );
		$out[ 'page:' . $key ][ $k ] = $id;
	}

	return $out;
}

/** Insert a published post (language set with Polylang) and remember it for « réimporter ». */
function wi_import_post( $type, $title, $slug, $order, $lang, $status = 'publish' ) {
	$id = wp_insert_post( array(
		'post_type'   => $type,
		'post_status' => $status,
		'post_title'  => $title,
		'post_name'   => $lang && 'page' !== $type && function_exists( 'pll_default_language' ) && pll_default_language() !== $lang ? $slug . '-' . $lang : $slug,
		'menu_order'  => (int) $order,
	) );
	if ( $lang && function_exists( 'pll_set_post_language' ) ) {
		pll_set_post_language( $id, $lang );
	}
	$state          = get_option( WI_IMPORT_OPTION, array() );
	$state['ids'][] = $id;
	update_option( WI_IMPORT_OPTION, $state, false );
	return $id;
}

/** Save field values (by field key), turning theme image paths into Media Library IDs. */
function wi_import_fields( $post_id, $group, $values ) {
	foreach ( $values as $name => $value ) {
		update_field( 'field_wi_' . $group . '_' . $name, wi_import_media_values( $value ), $post_id );
	}
}

function wi_import_is_path( $v ) {
	return is_string( $v ) && 0 === strpos( $v, 'assets/' ) && preg_match( '/\.(jpe?g|png|gif|webp|svg)$/i', $v );
}

/** 'assets/…jpg' → ID, [ 'assets/…jpg', 'alt' ] → ID with alt text, recursively. */
function wi_import_media_values( $value ) {
	if ( wi_import_is_path( $value ) ) {
		return wi_import_image( $value );
	}
	if ( is_array( $value ) ) {
		if ( 2 === count( $value ) && isset( $value[0], $value[1] ) && wi_import_is_path( $value[0] ) && ! wi_import_is_path( $value[1] ) ) {
			return wi_import_image( $value[0], $value[1] );
		}
		foreach ( $value as $k => $v ) {
			$value[ $k ] = wi_import_media_values( $v );
		}
	}
	return $value;
}

/** Copy an image of the theme into the Media Library (once; reused afterwards). */
function wi_import_image( $rel, $alt = '' ) {
	$state = get_option( WI_IMPORT_OPTION, array() );
	if ( ! empty( $state['media'][ $rel ] ) && get_post( $state['media'][ $rel ] ) ) {
		$id = (int) $state['media'][ $rel ];
	} else {
		$src = WI_DIR . '/' . $rel;
		if ( ! file_exists( $src ) ) {
			return 0;
		}
		$tmp = wp_tempnam( basename( $rel ) );
		copy( $src, $tmp );
		$id = media_handle_sideload( array( 'name' => basename( $rel ), 'tmp_name' => $tmp ), 0 );
		if ( is_wp_error( $id ) ) {
			@unlink( $tmp ); // phpcs:ignore
			return 0;
		}
		$state                  = get_option( WI_IMPORT_OPTION, array() );
		$state['media'][ $rel ] = $id;
		update_option( WI_IMPORT_OPTION, $state, false );
	}
	if ( $alt && ! get_post_meta( $id, '_wp_attachment_image_alt', true ) ) {
		update_post_meta( $id, '_wp_attachment_image_alt', $alt );
	}
	return $id;
}

function wi_import_norm( $name ) {
	return strtolower( trim( remove_accents( $name ) ) );
}
