<?php
/**
 * Small helpers shared by the templates.
 */

defined( 'ABSPATH' ) || exit;

/** Field value (Secure Custom Fields), with a fallback when the field is empty. */
function wi_field( $name, $post_id = null, $default = '' ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$value   = function_exists( 'get_field' ) ? get_field( $name, $post_id ) : get_post_meta( $post_id, $name, true );
	if ( '' === $value || null === $value || false === $value || array() === $value ) {
		return $default;
	}
	return $value;
}

/** Rows of a repeater field (always an array). */
function wi_rows( $name, $post_id = null ) {
	$rows = wi_field( $name, $post_id, array() );
	return is_array( $rows ) ? $rows : array();
}

/** URL of an image field value (attachment ID, array or URL). */
function wi_img( $image, $size = 'full' ) {
	if ( is_array( $image ) ) {
		$image = isset( $image['ID'] ) ? $image['ID'] : ( isset( $image['url'] ) ? $image['url'] : '' );
	}
	if ( is_numeric( $image ) && $image ) {
		$src = wp_get_attachment_image_url( (int) $image, $size );
		return $src ? $src : '';
	}
	return is_string( $image ) ? $image : '';
}

/** Alt text of an image field value. */
function wi_img_alt( $image ) {
	if ( is_array( $image ) ) {
		$image = isset( $image['ID'] ) ? $image['ID'] : 0;
	}
	return $image ? (string) get_post_meta( (int) $image, '_wp_attachment_image_alt', true ) : '';
}

/** URL of a file bundled with the theme (logos, fonts …). */
function wi_asset( $path ) {
	return WI_URI . '/' . ltrim( $path, '/' );
}

/* ---------------------------------------------------------------------------
   Translated labels. With Polylang they appear in « Langues › Traductions »
   (group « Wildlife Interactions ») so the English wording can be typed there.
   --------------------------------------------------------------------------- */

function wi_strings() {
	return array(
		// header / footer
		'menu'           => 'Menu',
		'mainNav'        => 'Navigation principale',
		'homeLabel'      => 'Wildlife Interactions — accueil',
		'languages'      => 'Langues',
		// detail panel (Missions & Éducation)
		'detailNav'      => 'Missions et programmes',
		'close'          => 'Fermer',
		'overview'       => 'Aperçu',
		'issues'         => 'Enjeux',
		'issuesTitle'    => 'Pourquoi agir maintenant ?',
		'talentActivities' => 'Activités WI',
		'linkedinOf'     => 'LinkedIn de',
		'previous'       => 'Précédente',
		'next'           => 'Prochaine',
		// lists, globe, FAQ
		'listTitle'      => 'Missions et programmes éducatifs',
		'more'           => 'En savoir plus',
		'faqTitle'       => 'Questions fréquentes',
		'partners'       => 'Partenaires',
		'view'           => 'Affichage',
		'list'           => 'Liste',
		'map'            => 'Carte 3D',
		'toggleView'     => 'Basculer entre la liste et la carte 3D',
		'globeLabel'     => 'Carte 3D interactive : glisser pour faire tourner le globe, flèches du clavier pour le faire pivoter',
		'globeHint'      => 'Glisser pour faire tourner',
		'projects'       => 'projets',
		// talents, expo
		'bio'            => 'Bio',
		'talentDomains'  => 'Domaines',
		'ourTalents'     => 'Nos talents',
		'page'           => 'Page',
		'pagePrev'       => 'Page précédente',
		'pageNext'       => 'Page suivante',
		'pages'          => 'Pages',
		'download'       => 'Télécharger',
		'tvTopics'       => 'Sujets TV',
		'magazines'      => 'Magazines',
		// contact form
		'contactSubject' => 'Objet de votre message',
		'firstname'      => 'Prénom',
		'lastname'       => 'Nom',
		'email'          => 'E-mail',
		'phone'          => 'Téléphone (optionnel)',
		'company'        => 'Entreprise (optionnel)',
		'message'        => 'Message',
		'send'           => 'Envoyer',
		'formRequired'   => 'Merci de compléter les champs obligatoires.',
		'formSending'    => 'Envoi en cours…',
		'formSent'       => 'Merci ! Votre message a bien été envoyé.',
		'formError'      => "Désolé, l'envoi a échoué. Écrivez-nous directement par e-mail.",
	);
}

add_action( 'init', function () {
	if ( function_exists( 'pll_register_string' ) ) {
		foreach ( wi_strings() as $key => $text ) {
			pll_register_string( $key, $text, 'Wildlife Interactions', strlen( $text ) > 60 );
		}
	}
} );

/** Translated label by key. */
function wi_t( $key ) {
	$strings = wi_strings();
	$text    = isset( $strings[ $key ] ) ? $strings[ $key ] : $key;
	return function_exists( 'pll__' ) ? pll__( $text ) : $text;
}

/** All labels, translated (for the scripts: window.WI_I18N). */
function wi_all_strings() {
	$out = array();
	foreach ( array_keys( wi_strings() ) as $key ) {
		$out[ $key ] = wi_t( $key );
	}
	return $out;
}

/* ---------------------------------------------------------------------------
   Pages
   --------------------------------------------------------------------------- */

/** Current language slug ('' without Polylang). */
function wi_lang() {
	return function_exists( 'pll_current_language' ) ? (string) pll_current_language() : '';
}

/** ID of the front page in the current language. Holds the site-wide settings. */
function wi_front_id() {
	$id = (int) get_option( 'page_on_front' );
	if ( $id && function_exists( 'pll_get_post' ) ) {
		$translated = pll_get_post( $id );
		if ( $translated ) {
			$id = (int) $translated;
		}
	}
	return $id;
}

/** Site-wide setting (stored on the front page, « Réglages du site » tab). */
function wi_setting( $name, $default = '' ) {
	$front = wi_front_id();
	return $front ? wi_field( $name, $front, $default ) : $default;
}

/** Published pages using a template, in the current language. */
function wi_pages_with_template( $template ) {
	$args = array(
		'post_type'      => 'page',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order title',
		'order'          => 'ASC',
		'meta_key'       => '_wp_page_template', // phpcs:ignore WordPress.DB.SlowDBQuery
		'meta_value'     => $template,           // phpcs:ignore WordPress.DB.SlowDBQuery
	);
	if ( wi_lang() ) {
		$args['lang'] = wi_lang();
	}
	return get_posts( $args );
}

/** URL of the first page using a template (or the home page). */
function wi_page_url( $template ) {
	$pages = wi_pages_with_template( $template );
	return $pages ? get_permalink( $pages[0] ) : home_url( '/' );
}

/** The « Missions » / « Éducation » list pages, keyed by category. */
function wi_zone_pages() {
	$out = array();
	foreach ( wi_pages_with_template( 'templates/page-zones.php' ) as $page ) {
		$cat = wi_field( 'zone_category', $page->ID, 'missions' );
		if ( ! isset( $out[ $cat ] ) ) {
			$out[ $cat ] = $page;
		}
	}
	return $out;
}

/** Contact page URL, optionally with an anchor (#soutenir). */
function wi_contact_url( $anchor = '' ) {
	return wi_page_url( 'templates/page-contact.php' ) . ( $anchor ? '#' . $anchor : '' );
}

/** Text with *asterisks* turned into italics (species names), escaped. */
function wi_rich( $text ) {
	return preg_replace( '/\*([^*]+)\*/', '<em>$1</em>', esc_html( $text ) );
}
