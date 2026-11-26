<?php
/**
 * Edit screens (Secure Custom Fields field groups, declared in code so they
 * travel with the theme). Field names here are the ones read by the templates.
 */

defined( 'ABSPATH' ) || exit;

/* Tiny builders: wi_f( type, name, label, extra ) */
function wi_f( $type, $name, $label, $extra = array() ) {
	return array_merge( array( 'type' => $type, 'name' => $name, 'label' => $label ), $extra );
}
function wi_tab( $label ) {
	return array( 'type' => 'tab', 'name' => '', 'label' => $label, 'placement' => 'top' );
}
function wi_image( $name, $label, $extra = array() ) {
	return wi_f( 'image', $name, $label, array_merge( array( 'return_format' => 'id', 'preview_size' => 'medium', 'library' => 'all' ), $extra ) );
}
function wi_repeater( $name, $label, $sub, $extra = array() ) {
	return wi_f( 'repeater', $name, $label, array_merge( array( 'layout' => 'block', 'button_label' => 'Ajouter', 'sub_fields' => $sub ), $extra ) );
}
function wi_textarea( $name, $label, $rows = 4, $extra = array() ) {
	return wi_f( 'textarea', $name, $label, array_merge( array( 'rows' => $rows, 'new_lines' => '' ), $extra ) );
}
/* Unique keys: field_wi_<group>_<name>, sub fields field_wi_<group>_<parent>__<name>
   (double underscore, so « enjeux » › « title » can't clash with « enjeux_title »).
   Never change a key or a name once the site is live: saved content is linked to them. */
function wi_keys( array $fields, $prefix, $sep = '_' ) {
	foreach ( $fields as $i => $field ) {
		$name           = $field['name'] ? $field['name'] : 'tab' . $i;
		$field['key']   = $prefix . $sep . $name;
		if ( ! empty( $field['sub_fields'] ) ) {
			$field['sub_fields'] = wi_keys( $field['sub_fields'], $field['key'], '__' );
		}
		$fields[ $i ] = $field;
	}
	return $fields;
}
function wi_group( $key, $title, $fields, $location, $extra = array() ) {
	acf_add_local_field_group( array_merge( array(
		'key'                   => 'group_wi_' . $key,
		'title'                 => $title,
		'fields'                => wi_keys( $fields, 'field_wi_' . $key ),
		'location'              => $location,
		'position'              => 'normal',
		'style'                 => 'default',
		'label_placement'       => 'top',
		'hide_on_screen'        => array( 'the_content', 'excerpt', 'discussion', 'comments', 'author', 'format', 'categories', 'tags', 'send-trackbacks' ),
	), $extra ) );
}
function wi_is( $param, $value ) {
	return array( array( array( 'param' => $param, 'operator' => '==', 'value' => $value ) ) );
}

add_action( 'acf/include_fields', function () {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}
	$stat = array(
		wi_f( 'text', 'value', 'Chiffre', array( 'wrapper' => array( 'width' => 25 ), 'placeholder' => '87 500' ) ),
		wi_f( 'text', 'label', 'Unité / libellé', array( 'wrapper' => array( 'width' => 25 ), 'placeholder' => 'km²' ) ),
		wi_textarea( 'text', 'Texte', 2, array( 'wrapper' => array( 'width' => 50 ) ) ),
	);
	$italic = 'Astuce : entourez un mot d’astérisques pour le mettre en italique, ex. *Balaenoptera physalus*.';

	/* ------------------------------------------------------------------ Missions & Éducation */
	wi_group( 'zone', 'Contenu', array(
		wi_tab( 'Liste' ),
		wi_f( 'select', 'category', 'Catégorie', array( 'choices' => array( 'missions' => 'Missions', 'education' => 'Éducation' ), 'default_value' => 'missions', 'wrapper' => array( 'width' => 30 ) ) ),
		wi_textarea( 'summary', 'Résumé (sous le nom, dans la liste)', 2, array( 'wrapper' => array( 'width' => 70 ) ) ),
		wi_image( 'image', 'Image principale', array( 'instructions' => 'Grande photo de la page détail (et fond si « Image de fond » est vide).', 'wrapper' => array( 'width' => 50 ) ) ),
		wi_image( 'bg', 'Image de fond', array( 'instructions' => 'Facultatif. Affichée en pleine luminosité.', 'wrapper' => array( 'width' => 50 ) ) ),

		wi_tab( 'Aperçu & enjeux' ),
		wi_textarea( 'lead', 'Texte d’aperçu', 6 ),
		wi_f( 'text', 'enjeux_title', 'Titre des enjeux', array( 'placeholder' => 'Pourquoi agir maintenant ?', 'instructions' => 'Laisser vide pour « Pourquoi agir maintenant ? ».' ) ),
		wi_image( 'enjeux_image', 'Image du premier enjeu' ),
		wi_repeater( 'enjeux', 'Enjeux (4)', array(
			wi_f( 'text', 'title', 'Titre', array( 'wrapper' => array( 'width' => 40 ) ) ),
			wi_textarea( 'text', 'Texte', 3, array( 'wrapper' => array( 'width' => 60 ) ) ),
		), array( 'min' => 4, 'max' => 4, 'layout' => 'table' ) ),
		wi_repeater( 'stats', 'Chiffres clés (3)', $stat, array( 'min' => 0, 'max' => 3, 'layout' => 'table' ) ),

		wi_tab( 'Sections' ),
		wi_repeater( 'sections', 'Sections de texte', array(
			wi_f( 'text', 'tag', 'Étiquette', array( 'wrapper' => array( 'width' => 25 ), 'placeholder' => 'Écosystème' ) ),
			wi_f( 'text', 'title', 'Titre', array( 'wrapper' => array( 'width' => 75 ) ) ),
			wi_textarea( 'text', 'Texte', 6, array( 'instructions' => $italic ) ),
		), array( 'button_label' => 'Ajouter une section' ) ),
		wi_repeater( 'more_stats', 'Chiffres (bas de page, 3)', $stat, array( 'max' => 3, 'layout' => 'table' ) ),

		wi_tab( 'Équipe' ),
		wi_f( 'text', 'team_tag', 'Étiquette', array( 'placeholder' => 'Terrain', 'wrapper' => array( 'width' => 30 ) ) ),
		wi_f( 'text', 'team_title', 'Titre', array( 'placeholder' => 'Les vigies de Pelagos', 'wrapper' => array( 'width' => 70 ) ) ),
		wi_repeater( 'team', 'Membres', array(
			wi_f( 'post_object', 'talent', 'Talent', array( 'post_type' => array( 'wi_talent' ), 'return_format' => 'id', 'ui' => 1, 'wrapper' => array( 'width' => 50 ) ) ),
			wi_f( 'text', 'role', 'Rôle dans cette mission', array( 'wrapper' => array( 'width' => 50 ) ) ),
		), array( 'layout' => 'table', 'button_label' => 'Ajouter un membre', 'instructions' => 'La photo, la bio et le LinkedIn viennent de la fiche du talent.' ) ),

		wi_tab( 'Carte 3D' ),
		wi_f( 'message', '', '', array( 'message' => 'Position du drapeau sur le globe. Plusieurs éléments avec le même nom de lieu partagent un drapeau. Trouver les coordonnées : clic droit sur Google Maps → la première ligne donne « latitude, longitude ».' ) ),
		wi_f( 'text', 'place', 'Nom du lieu', array( 'placeholder' => 'Sanctuaire Pelagos', 'wrapper' => array( 'width' => 40 ) ) ),
		wi_f( 'number', 'lat', 'Latitude', array( 'step' => 'any', 'wrapper' => array( 'width' => 30 ) ) ),
		wi_f( 'number', 'lon', 'Longitude', array( 'step' => 'any', 'wrapper' => array( 'width' => 30 ) ) ),
		wi_f( 'number', 'stem', 'Hauteur du drapeau (px)', array( 'default_value' => 48, 'instructions' => 'Pour éviter que deux drapeaux proches se chevauchent.', 'wrapper' => array( 'width' => 50 ) ) ),
		wi_f( 'select', 'side', 'Côté de l’étiquette', array( 'choices' => array( 'right' => 'Droite', 'left' => 'Gauche' ), 'default_value' => 'right', 'wrapper' => array( 'width' => 50 ) ) ),
	), wi_is( 'post_type', 'wi_zone' ) );

	/* ------------------------------------------------------------------ Talents */
	wi_group( 'talent', 'Profil', array(
		wi_f( 'text', 'role', 'Rôle / organisation', array( 'placeholder' => 'Glaciologue, Université du Svalbard' ) ),
		wi_textarea( 'bio', 'Bio', 5 ),
		wi_f( 'url', 'linkedin', 'Lien LinkedIn' ),
	), wi_is( 'post_type', 'wi_talent' ), array( 'instructions' => '' ) );

	/* ------------------------------------------------------------------ Home + site-wide settings */
	wi_group( 'home', 'Page d’accueil', array(
		wi_tab( 'Introduction' ),
		wi_f( 'text', 'hero_title', 'Titre', array( 'default_value' => 'Wildlife Interactions' ) ),
		wi_textarea( 'hero_subtitle', 'Sous-titre', 2 ),
		wi_image( 'hero_bg', 'Image de fond', array( 'instructions' => 'Facultatif : sinon la première mission.', 'wrapper' => array( 'width' => 50 ) ) ),
		wi_repeater( 'partners', 'Logos partenaires', array(
			wi_image( 'logo', 'Logo (clair, sur fond sombre)', array( 'preview_size' => 'thumbnail', 'wrapper' => array( 'width' => 40 ) ) ),
			wi_f( 'text', 'name', 'Nom', array( 'wrapper' => array( 'width' => 60 ) ) ),
		), array( 'layout' => 'table' ) ),
		wi_repeater( 'figures', 'Chiffres', array(
			wi_f( 'text', 'value', 'Chiffre', array( 'placeholder' => '3 000+' ) ),
			wi_f( 'text', 'label', 'Libellé', array( 'placeholder' => 'Élèves par an' ) ),
		), array( 'layout' => 'table', 'max' => 4 ) ),

		wi_tab( 'Questions fréquentes' ),
		wi_image( 'faq_bg', 'Image de fond', array( 'wrapper' => array( 'width' => 50 ) ) ),
		wi_repeater( 'faq', 'Questions', array(
			wi_f( 'text', 'q', 'Question' ),
			wi_textarea( 'a', 'Réponse', 3 ),
		), array( 'button_label' => 'Ajouter une question' ) ),

		wi_tab( 'Réglages du site' ),
		wi_f( 'message', '', '', array( 'message' => 'Ces réglages s’appliquent à tout le site (menu, pied de page, contact).' ) ),
		wi_f( 'text', 'support_label', 'Bouton « Nous soutenir »', array( 'default_value' => 'Nous soutenir', 'wrapper' => array( 'width' => 50 ) ) ),
		wi_f( 'text', 'contact_label', 'Bouton « Contact » (pied de page)', array( 'default_value' => 'Contact', 'wrapper' => array( 'width' => 50 ) ) ),
		wi_f( 'email', 'email', 'E-mail de contact', array( 'default_value' => 'contact@wildlife-interactions.com', 'wrapper' => array( 'width' => 50 ) ) ),
		wi_f( 'url', 'linkedin', 'Page LinkedIn', array( 'default_value' => 'https://www.linkedin.com/', 'wrapper' => array( 'width' => 50 ) ) ),
		wi_f( 'text', 'footer_claim', 'Pied de page : titre', array( 'default_value' => 'Art. Éducation. Science' ) ),
		wi_f( 'text', 'footer_tagline', 'Pied de page : phrase', array( 'default_value' => "Ensemble, au service d'un impact durable pour la vie marine" ) ),
		wi_f( 'text', 'footer_copyright', 'Pied de page : copyright', array( 'default_value' => '© 2026 Wildlife Interactions. Tous droits réservés.' ) ),
	), wi_is( 'page_type', 'front_page' ) );

	/* ------------------------------------------------------------------ Missions / Éducation list pages */
	wi_group( 'zones_page', 'Liste', array(
		wi_f( 'select', 'zone_category', 'Afficher', array(
			'choices'       => array( 'missions' => 'Les missions', 'education' => 'Les programmes d’éducation' ),
			'default_value' => 'missions',
			'instructions'  => 'Le contenu se modifie dans le menu « Missions & Éducation ». Le titre de cette page sert de nom de catégorie.',
		) ),
	), wi_is( 'page_template', 'templates/page-zones.php' ) );

	/* ------------------------------------------------------------------ Talents page */
	wi_group( 'talents_page', 'Page Talents', array(
		wi_f( 'text', 'hero_title', 'Titre', array( 'default_value' => 'Talent WI' ) ),
		wi_textarea( 'hero_subtitle', 'Sous-titre', 2 ),
		wi_image( 'hero_bg', 'Image de fond' ),
		wi_repeater( 'categories', 'Cartes du haut (5)', array(
			wi_f( 'text', 'label', 'Domaine', array( 'instructions' => 'Vide = carte photo (fondateur).', 'wrapper' => array( 'width' => 30 ) ) ),
			wi_f( 'post_object', 'talent', 'Talent (photo au survol)', array( 'post_type' => array( 'wi_talent' ), 'return_format' => 'id', 'ui' => 1, 'wrapper' => array( 'width' => 40 ) ) ),
			wi_f( 'text', 'alt', 'Texte alternatif', array( 'wrapper' => array( 'width' => 30 ) ) ),
		), array( 'layout' => 'table', 'max' => 5, 'instructions' => 'Les profils se gèrent dans le menu « Talents ».' ) ),
	), wi_is( 'page_template', 'templates/page-talents.php' ) );

	/* ------------------------------------------------------------------ Exposition & média */
	$media = array(
		wi_image( 'image', 'Image', array( 'wrapper' => array( 'width' => 30 ) ) ),
		wi_f( 'text', 'title', 'Titre', array( 'wrapper' => array( 'width' => 25 ) ) ),
		wi_f( 'text', 'meta', 'Lieu, année', array( 'wrapper' => array( 'width' => 20 ) ) ),
		wi_f( 'url', 'link', 'Lien (facultatif)', array( 'wrapper' => array( 'width' => 25 ) ) ),
	);
	wi_group( 'expo', 'Page Exposition & média', array(
		wi_tab( 'En-tête' ),
		wi_f( 'text', 'hero_title', 'Titre', array( 'default_value' => 'Exposition & média' ) ),
		wi_textarea( 'hero_subtitle', 'Sous-titre', 2 ),
		wi_image( 'hero_bg', 'Image de fond' ),
		wi_f( 'gallery', 'hero_floats', 'Photos révélées au survol (4)', array( 'return_format' => 'id', 'max' => 4, 'preview_size' => 'thumbnail' ) ),
		wi_image( 'hero_video_image', 'Image « vidéo »', array( 'wrapper' => array( 'width' => 50 ) ) ),
		wi_f( 'url', 'hero_video_link', 'Lien de la vidéo (facultatif)', array( 'wrapper' => array( 'width' => 50 ) ) ),

		wi_tab( 'Exposition' ),
		wi_f( 'text', 'expo_title', 'Titre', array( 'wrapper' => array( 'width' => 60 ) ) ),
		wi_f( 'text', 'expo_cta', 'Bouton', array( 'default_value' => 'Nous contacter', 'wrapper' => array( 'width' => 40 ) ) ),
		wi_f( 'text', 'expo_tag', 'Étiquette' ),
		wi_textarea( 'expo_text', 'Texte', 6 ),
		wi_f( 'group', 'expo_media_1', 'Photo 1', array( 'layout' => 'table', 'sub_fields' => $media ) ),
		wi_f( 'group', 'expo_media_2', 'Photo 2 (+ affiche)', array( 'layout' => 'table', 'sub_fields' => array_merge( $media, array( wi_image( 'poster', 'Affiche', array( 'wrapper' => array( 'width' => 30 ) ) ) ) ) ) ),

		wi_tab( 'Sujets TV' ),
		wi_f( 'text', 'tv_tag', 'Étiquette', array( 'default_value' => 'Sujet TV' ) ),
		wi_textarea( 'tv_text', 'Texte', 6 ),
		wi_repeater( 'tv', 'Sujets', $media, array( 'layout' => 'table', 'button_label' => 'Ajouter un sujet' ) ),

		wi_tab( 'Magazines' ),
		wi_f( 'text', 'mag_tag', 'Étiquette', array( 'default_value' => 'Magazines' ) ),
		wi_textarea( 'mag_text', 'Texte', 6 ),
		wi_repeater( 'magazines', 'Magazines', array(
			wi_f( 'text', 'name', 'Nom', array( 'wrapper' => array( 'width' => 50 ) ) ),
			wi_image( 'cover', 'Couverture', array( 'wrapper' => array( 'width' => 50 ) ) ),
			wi_f( 'gallery', 'pages', 'Pages (carrousel)', array( 'return_format' => 'id', 'preview_size' => 'thumbnail' ) ),
			wi_f( 'file', 'download', 'Fichier à télécharger (facultatif)', array( 'return_format' => 'url' ) ),
		), array( 'button_label' => 'Ajouter un magazine' ) ),

		wi_tab( 'Partenaires' ),
		wi_f( 'text', 'partners_title', 'Titre', array( 'default_value' => 'Partenaires' ) ),
		wi_repeater( 'partners', 'Logos', array(
			wi_image( 'logo', 'Logo (sur fond clair)', array( 'preview_size' => 'thumbnail', 'wrapper' => array( 'width' => 40 ) ) ),
			wi_f( 'text', 'name', 'Nom', array( 'wrapper' => array( 'width' => 60 ) ) ),
		), array( 'layout' => 'table' ) ),
	), wi_is( 'page_template', 'templates/page-expo.php' ) );

	/* ------------------------------------------------------------------ Contact */
	wi_group( 'contact', 'Page Contact', array(
		wi_f( 'text', 'contact_title', 'Titre', array( 'default_value' => 'Comment pouvons-nous vous aider ?' ) ),
		wi_f( 'gallery', 'images', 'Photos animées (3)', array( 'return_format' => 'id', 'preview_size' => 'thumbnail' ) ),
		wi_repeater( 'options', 'Choix « Objet du message »', array(
			wi_f( 'text', 'label', 'Bouton', array( 'wrapper' => array( 'width' => 30 ) ) ),
			wi_f( 'text', 'key', 'Identifiant', array( 'instructions' => 'Sans espace ni accent. « partenariat » s’ouvre aussi depuis « Nous soutenir ».', 'wrapper' => array( 'width' => 20 ) ) ),
			wi_textarea( 'intro', 'Texte affiché', 2, array( 'wrapper' => array( 'width' => 50 ) ) ),
		), array( 'layout' => 'table', 'max' => 4 ) ),
		wi_f( 'email', 'recipient', 'Les messages sont envoyés à', array( 'instructions' => 'Vide = l’e-mail de contact (Accueil › Réglages du site).' ) ),
	), wi_is( 'page_template', 'templates/page-contact.php' ) );
} );
