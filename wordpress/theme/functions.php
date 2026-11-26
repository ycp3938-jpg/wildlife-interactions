<?php
/**
 * Wildlife Interactions — theme bootstrap.
 *
 * inc/helpers.php        fields, images, translated labels, page lookups
 * inc/content-types.php  « Missions & Éducation » and « Talents » post types
 * inc/fields.php         Secure Custom Fields field groups (edit screens)
 * inc/assets.php         CSS / JS per page + the data the scripts read (window.WI_*)
 * inc/template-tags.php  header, menu, footer, language switch
 * inc/contact.php        contact form → e-mail
 * inc/importer.php       one-click import of the current site content (Outils › Importer le site WI)
 */

defined( 'ABSPATH' ) || exit;

define( 'WI_VERSION', '1.0.0' );
define( 'WI_DIR', get_template_directory() );
define( 'WI_URI', get_template_directory_uri() );

require WI_DIR . '/inc/helpers.php';
require WI_DIR . '/inc/content-types.php';
require WI_DIR . '/inc/fields.php';
require WI_DIR . '/inc/assets.php';
require WI_DIR . '/inc/template-tags.php';
require WI_DIR . '/inc/contact.php';
require WI_DIR . '/inc/importer.php';

add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'script', 'style', 'gallery', 'caption' ) );
	register_nav_menus( array( 'primary' => 'Menu principal' ) );
} );

/* The theme needs Secure Custom Fields (free, by WordPress.org) for its edit screens. */
add_action( 'admin_notices', function () {
	if ( function_exists( 'get_field' ) || ! current_user_can( 'install_plugins' ) ) {
		return;
	}
	$url = admin_url( 'plugin-install.php?s=Secure+Custom+Fields&tab=search&type=term' );
	printf(
		'<div class="notice notice-warning"><p><strong>Wildlife Interactions :</strong> installez et activez l’extension <a href="%s">Secure Custom Fields</a> pour pouvoir modifier les contenus du site.</p></div>',
		esc_url( $url )
	);
} );
