<?php
/**
 * Post types the client edits:
 * - wi_zone   « Missions & Éducation » : list item, detail page, place on the 3D globe
 * - wi_talent « Talents »              : people (photo = featured image)
 * Order: « Attributs › Ordre » (lowest first).
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', function () {
	register_post_type( 'wi_zone', array(
		'labels'        => array(
			'name'          => 'Missions & Éducation',
			'singular_name' => 'Mission / programme',
			'menu_name'     => 'Missions & Éducation',
			'add_new'       => 'Ajouter',
			'add_new_item'  => 'Ajouter une mission ou un programme',
			'edit_item'     => 'Modifier',
			'new_item'      => 'Nouveau',
			'view_item'     => 'Voir',
			'search_items'  => 'Rechercher',
			'not_found'     => 'Aucun élément',
			'all_items'     => 'Tous les éléments',
		),
		'public'        => false, // shown inside the Missions / Éducation pages (detail panel), not on own URLs
		'show_ui'       => true,
		'show_in_rest'  => true,
		'menu_position' => 20,
		'menu_icon'     => 'dashicons-admin-site-alt3',
		'supports'      => array( 'title', 'page-attributes' ),
	) );

	register_post_type( 'wi_talent', array(
		'labels'        => array(
			'name'               => 'Talents',
			'singular_name'      => 'Talent',
			'add_new'            => 'Ajouter',
			'add_new_item'       => 'Ajouter un talent',
			'edit_item'          => 'Modifier le talent',
			'search_items'       => 'Rechercher',
			'not_found'          => 'Aucun talent',
			'all_items'          => 'Tous les talents',
			'featured_image'     => 'Photo',
			'set_featured_image' => 'Choisir la photo',
			'remove_featured_image' => 'Retirer la photo',
		),
		'public'        => false,
		'show_ui'       => true,
		'show_in_rest'  => true,
		'menu_position' => 21,
		'menu_icon'     => 'dashicons-groups',
		'supports'      => array( 'title', 'thumbnail', 'page-attributes' ),
	) );
} );

/* Polylang: both post types are translatable. */
add_filter( 'pll_get_post_types', function ( $types ) {
	$types['wi_zone']   = 'wi_zone';
	$types['wi_talent'] = 'wi_talent';
	return $types;
} );

/* Placeholder of the title field. */
add_filter( 'enter_title_here', function ( $text, $post ) {
	if ( 'wi_zone' === $post->post_type ) {
		return 'Nom (ex. Mer Méditerranée)';
	}
	if ( 'wi_talent' === $post->post_type ) {
		return 'Prénom Nom';
	}
	return $text;
}, 10, 2 );

/* Admin lists: show the order, the category and the photo; sort by order. */
add_filter( 'manage_wi_zone_posts_columns', function ( $cols ) {
	return array_slice( $cols, 0, 2, true ) + array( 'wi_cat' => 'Catégorie', 'wi_order' => 'Ordre' ) + array_slice( $cols, 2, null, true );
} );
add_filter( 'manage_wi_talent_posts_columns', function ( $cols ) {
	return array( 'cb' => $cols['cb'], 'wi_photo' => 'Photo' ) + array_slice( $cols, 1, 1, true ) + array( 'wi_role' => 'Rôle', 'wi_order' => 'Ordre' ) + array_slice( $cols, 2, null, true );
} );
$wi_columns = function ( $col, $post_id ) {
	if ( 'wi_order' === $col ) {
		echo (int) get_post_field( 'menu_order', $post_id );
	} elseif ( 'wi_cat' === $col ) {
		echo 'education' === wi_field( 'category', $post_id ) ? 'Éducation' : 'Missions';
	} elseif ( 'wi_role' === $col ) {
		echo esc_html( wi_field( 'role', $post_id ) );
	} elseif ( 'wi_photo' === $col ) {
		echo get_the_post_thumbnail( $post_id, array( 48, 48 ), array( 'style' => 'width:48px;height:48px;object-fit:cover;border-radius:4px' ) );
	}
};
add_action( 'manage_wi_zone_posts_custom_column', $wi_columns, 10, 2 );
add_action( 'manage_wi_talent_posts_custom_column', $wi_columns, 10, 2 );

add_action( 'pre_get_posts', function ( $query ) {
	if ( is_admin() && $query->is_main_query() && in_array( $query->get( 'post_type' ), array( 'wi_zone', 'wi_talent' ), true ) && ! $query->get( 'orderby' ) ) {
		$query->set( 'orderby', 'menu_order title' );
		$query->set( 'order', 'ASC' );
	}
} );

/** Published items of a post type, in order, current language. */
function wi_items( $post_type ) {
	$args = array(
		'post_type'      => $post_type,
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order title',
		'order'          => 'ASC',
	);
	if ( wi_lang() ) {
		$args['lang'] = wi_lang();
	}
	return get_posts( $args );
}
