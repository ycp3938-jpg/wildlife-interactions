<?php
/**
 * Styles and scripts per page (same files as the static site), plus the data
 * the scripts read — built from WordPress content instead of js/zones.js,
 * js/talents-data.js and js/detail-content.js.
 */

defined( 'ABSPATH' ) || exit;

/** Which kind of page is shown: home | zones | talents | expo | contact | default */
function wi_page_kind() {
	if ( is_front_page() ) {
		return 'home';
	}
	$map = array(
		'templates/page-zones.php'   => 'zones',
		'templates/page-talents.php' => 'talents',
		'templates/page-expo.php'    => 'expo',
		'templates/page-contact.php' => 'contact',
	);
	$tpl = is_page() ? get_page_template_slug() : '';
	return isset( $map[ $tpl ] ) ? $map[ $tpl ] : 'default';
}

add_action( 'wp_enqueue_scripts', function () {
	$kind = wi_page_kind();
	$v    = WI_VERSION;

	$css = array( 'fonts', 'tokens', 'base', 'components' );
	if ( in_array( $kind, array( 'home', 'zones', 'default' ), true ) ) {
		$css = array_merge( $css, array( 'hero', 'detail' ) );
	}
	$css = array_merge( $css, array(
		'home'    => array( 'pages/home', 'globe' ),
		'zones'   => array( 'globe' ),
		'talents' => array( 'pages/talents' ),
		'expo'    => array( 'pages/talents', 'pages/expo' ),
		'contact' => array( 'pages/contact' ),
		'default' => array(),
	)[ $kind ], array( 'motion' ) );

	$prev = array();
	foreach ( $css as $file ) {
		$handle = 'wi-' . str_replace( '/', '-', $file );
		wp_enqueue_style( $handle, WI_URI . "/css/$file.css", $prev, $v );
		$prev = array( $handle );
	}
	wp_enqueue_style( 'wi-wp', WI_URI . '/wp.css', $prev, $v );

	$js = function ( $handle, $file, $deps = array() ) use ( $v ) {
		wp_enqueue_script( $handle, WI_URI . "/js/$file.js", $deps, $v, true );
	};
	$js( 'wi-main', 'main' );
	wp_add_inline_script( 'wi-main', wi_inline_data( $kind ), 'before' );

	if ( in_array( $kind, array( 'home', 'zones' ), true ) ) {
		$js( 'wi-activities', 'activities', array( 'wi-main' ) );
		$js( 'd3-array', 'vendor/d3-array.min' );
		$js( 'd3-geo', 'vendor/d3-geo.min', array( 'd3-array' ) );
		$js( 'topojson', 'vendor/topojson-client.min' );
		$js( 'wi-world', 'vendor/world-110m' );
		$js( 'wi-globe', 'globe', array( 'wi-activities', 'd3-geo', 'topojson', 'wi-world' ) );
	}
	if ( in_array( $kind, array( 'talents', 'expo', 'contact' ), true ) ) {
		$js( 'wi-pages', 'pages', array( 'wi-main' ) );
	}
	$js( 'lenis', 'vendor/lenis.min' );
	$js( 'wi-motion', 'motion', array( 'wi-main', 'lenis' ) );
} );

/* The site doesn't use the block editor on the front end: drop WordPress's default
   block / global styles and the emoji script so they can't interfere with css/. */
add_action( 'wp_enqueue_scripts', function () {
	foreach ( array( 'wp-block-library', 'wp-block-library-theme', 'classic-theme-styles', 'global-styles' ) as $handle ) {
		wp_dequeue_style( $handle );
	}
}, 100 );
remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
remove_action( 'wp_footer', 'wp_enqueue_global_styles', 1 );
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'wp_enqueue_scripts', 'wp_enqueue_emoji_styles' );

/* Keep the client from editing theme files in the admin (they'd be replaced by the next deployment). */
if ( ! defined( 'DISALLOW_FILE_EDIT' ) ) {
	define( 'DISALLOW_FILE_EDIT', true );
}

/** window.WI_* = …  (only what the current page needs) */
function wi_inline_data( $kind ) {
	$data = array(
		'WI_THEME_URL' => WI_URI . '/',
		'WI_I18N'      => wi_all_strings(),
	);
	if ( in_array( $kind, array( 'home', 'zones', 'talents' ), true ) ) {
		$data['WI_TALENTS'] = wi_data_talents();
	}
	if ( in_array( $kind, array( 'home', 'zones' ), true ) ) {
		$data = array_merge( $data, wi_data_zones() );
	}
	if ( 'talents' === $kind ) {
		$data['WI_TALENT_CATEGORIES'] = wi_data_talent_categories( get_queried_object_id() );
	}
	if ( 'contact' === $kind ) {
		$data['WI_CONTACT'] = array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'wi_contact' ),
		);
	}
	$out = '';
	foreach ( $data as $name => $value ) {
		$out .= 'window.' . $name . ' = ' . wp_json_encode( $value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG ) . ";\n";
	}
	return $out;
}

/** Talent post → the shape of js/talents-data.js (photo = image URL). */
function wi_talent_data( $post ) {
	$photo = get_the_post_thumbnail_url( $post, 'large' );
	return array(
		'name'     => get_the_title( $post ),
		'role'     => wi_field( 'role', $post->ID ),
		'photo'    => $photo ? $photo : null,
		'bio'      => wi_field( 'bio', $post->ID ),
		'linkedin' => wi_field( 'linkedin', $post->ID ),
	);
}

function wi_data_talents() {
	return array_map( 'wi_talent_data', wi_items( 'wi_talent' ) );
}

function wi_data_talent_categories( $page_id ) {
	$out = array();
	foreach ( wi_rows( 'categories', $page_id ) as $row ) {
		$talent = ! empty( $row['talent'] ) ? get_post( (int) $row['talent'] ) : null;
		if ( ! $talent ) {
			continue;
		}
		$photo = get_the_post_thumbnail_url( $talent, 'large' );
		$out[] = array(
			'label' => '' !== trim( (string) $row['label'] ) ? $row['label'] : null,
			'photo' => $photo ? $photo : '',
			'alt'   => isset( $row['alt'] ) ? $row['alt'] : '',
		);
	}
	return $out;
}

function wi_stat_rows( $rows ) {
	return array_values( array_map( function ( $r ) {
		return array( 'value' => (string) $r['value'], 'label' => (string) $r['label'], 'text' => (string) $r['text'] );
	}, $rows ) );
}

/** Missions & Éducation → WI_ZONES, WI_CATEGORIES, WI_DETAIL_MORE, WI_DETAIL_SHARED, WI_PLACES, WI_FAQ */
function wi_data_zones() {
	$zones  = array();
	$more   = array();
	$places = array();

	foreach ( wi_items( 'wi_zone' ) as $post ) {
		$id    = $post->ID;
		$slug  = $post->post_name ? $post->post_name : 'zone-' . $id;
		$image = wi_img( wi_field( 'image', $id ) );
		$bg    = wi_img( wi_field( 'bg', $id ) );
		$enjeux = array_values( array_map( function ( $r ) {
			return array( 'title' => (string) $r['title'], 'text' => (string) $r['text'] );
		}, wi_rows( 'enjeux', $id ) ) );
		while ( count( $enjeux ) < 4 ) {
			$enjeux[] = array( 'title' => '', 'text' => '' );
		}
		$zones[] = array(
			'slug'      => $slug,
			'category'  => wi_field( 'category', $id, 'missions' ),
			'name'      => get_the_title( $post ),
			'summary'   => wi_field( 'summary', $id ),
			'bg'        => $bg ? $bg : $image,
			'bgOpacity' => (float) wi_field( 'bg_opacity', $id, 0.5 ),
			'image'     => $image,
			'detail'    => array(
				'lead'        => wi_field( 'lead', $id ),
				'enjeuxTitle' => wi_field( 'enjeux_title', $id ),
				'enjeuxImage' => wi_img( wi_field( 'enjeux_image', $id ) ),
				'enjeux'      => $enjeux,
				'stats'       => wi_stat_rows( wi_rows( 'stats', $id ) ),
			),
		);

		$people = array();
		foreach ( wi_rows( 'team', $id ) as $row ) {
			if ( ! empty( $row['talent'] ) && get_post_status( (int) $row['talent'] ) ) {
				$people[] = array( get_the_title( (int) $row['talent'] ), (string) $row['role'] );
			}
		}
		$more[ $slug ] = array(
			'sections' => array_values( array_map( function ( $r ) {
				return array( 'tag' => (string) $r['tag'], 'title' => (string) $r['title'], 'text' => (string) $r['text'] );
			}, wi_rows( 'sections', $id ) ) ),
			'stats'    => wi_stat_rows( wi_rows( 'more_stats', $id ) ),
		);
		if ( $people ) {
			$more[ $slug ]['team'] = array(
				'tag'    => wi_field( 'team_tag', $id ),
				'title'  => wi_field( 'team_title', $id ),
				'people' => $people,
			);
		}

		$place = trim( (string) wi_field( 'place', $id ) );
		$lat   = wi_field( 'lat', $id, null );
		$lon   = wi_field( 'lon', $id, null );
		if ( '' !== $place && is_numeric( $lat ) && is_numeric( $lon ) ) {
			if ( ! isset( $places[ $place ] ) ) {
				$places[ $place ] = array(
					'name'  => $place,
					'lon'   => (float) $lon,
					'lat'   => (float) $lat,
					'stem'  => (int) wi_field( 'stem', $id, 48 ),
					'side'  => wi_field( 'side', $id, 'right' ),
					'slugs' => array(),
				);
			}
			$places[ $place ]['slugs'][] = $slug;
		}
	}

	$categories = array();
	$pages      = wi_zone_pages();
	foreach ( array( 'missions' => 'Missions', 'education' => 'Éducation' ) as $key => $label ) {
		$title              = isset( $pages[ $key ] ) ? get_the_title( $pages[ $key ] ) : $label;
		$categories[ $key ] = array(
			'label'    => $title,
			'navLabel' => function_exists( 'mb_strtoupper' ) ? mb_strtoupper( $title ) : strtoupper( $title ),
			'page'     => isset( $pages[ $key ] ) ? get_permalink( $pages[ $key ] ) : '',
		);
	}

	$front = wi_front_id();
	$faq   = array(
		'bg'        => wi_img( wi_field( 'faq_bg', $front ) ),
		'bgOpacity' => (float) wi_field( 'faq_bg_opacity', $front, 0.1 ),
		'items'     => array_values( array_map( function ( $r ) {
			return array( 'q' => (string) $r['q'], 'a' => (string) $r['a'] );
		}, wi_rows( 'faq', $front ) ) ),
	);

	return array(
		'WI_ZONES'         => $zones,
		'WI_CATEGORIES'    => $categories,
		'WI_DETAIL_MORE'   => $more,
		// no placeholder text on the live site: empty parts are simply left out
		'WI_DETAIL_SHARED' => array( 'sections' => array(), 'stats' => array(), 'talentsTitle' => '', 'talents' => array() ),
		'WI_PLACES'        => array_values( $places ),
		'WI_FAQ'           => $faq,
	);
}
