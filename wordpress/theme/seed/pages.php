<?php
/**
 * Text and images of the pages, as on the current static site (French).
 * Used once by the importer (Outils › Importer le site WI). Image paths are
 * relative to the theme folder. Missions, Éducation and Talents: seed/content.json.
 */

defined( 'ABSPATH' ) || exit;

return array(
	'home'     => array(
		'title'  => 'Accueil',
		'slug'   => 'accueil',
		'slug_en' => 'home',
		'order'  => 0,
		'fields' => array(
			'hero_title'       => 'Wildlife Interactions',
			'hero_subtitle'    => 'Une association dédiée à la protection des écosystèmes marins, unissant art, éducation et science pour un impact durable.',
			'hero_bg_opacity'  => 0.35,
			'partners'         => array(
				array( 'logo' => 'assets/logos/partner-fondation-prince-albert.png', 'name' => 'Fondation Prince Albert II de Monaco' ),
				array( 'logo' => 'assets/logos/partner-gouvernement-princier.png', 'name' => 'Gouvernement Princier — Principauté de Monaco' ),
				array( 'logo' => 'assets/logos/partner-centre-scientifique.png', 'name' => 'Centre Scientifique de Monaco' ),
				array( 'logo' => 'assets/logos/partner-energy-observer.png', 'name' => 'Energy Observer Foundation' ),
				array( 'logo' => 'assets/logos/partner-polarx.svg', 'name' => 'PolarX' ),
			),
			'figures'          => array(
				array( 'value' => '3 000+', 'label' => 'Élèves par an' ),
				array( 'value' => '10+', 'label' => 'Pays' ),
				array( 'value' => '10+', 'label' => 'Laboratoires' ),
			),
			// faq + faq_bg come from content.json
			'support_label'    => 'Nous soutenir',
			'contact_label'    => 'Contact',
			'email'            => 'contact@wildlife-interactions.com',
			'linkedin'         => 'https://www.linkedin.com/',
			'footer_claim'     => 'Art. Éducation. Science',
			'footer_tagline'   => "Ensemble, au service d'un impact durable pour la vie marine",
			'footer_copyright' => '© 2026 Wildlife Interactions. Tous droits réservés.',
		),
	),
	'missions'  => array(
		'title'    => 'Missions',
		'slug'     => 'missions',
		'slug_en'  => 'our-missions',
		'order'    => 1,
		'template' => 'templates/page-zones.php',
		'fields'   => array( 'zone_category' => 'missions' ),
	),
	'education' => array(
		'title'    => 'Éducation',
		'slug'     => 'education',
		'slug_en'  => 'education-programs',
		'order'    => 2,
		'template' => 'templates/page-zones.php',
		'fields'   => array( 'zone_category' => 'education' ),
	),
	'talents'   => array(
		'title'    => 'Talents',
		'slug'     => 'talents',
		'slug_en'  => 'our-talents',
		'order'    => 3,
		'template' => 'templates/page-talents.php',
		'fields'   => array(
			'hero_title'    => 'Talent WI',
			'hero_subtitle' => 'Scientifiques, explorateurs, artistes et pédagogues réunis pour comprendre, raconter et protéger la vie marine.',
			'hero_bg'       => 'assets/img/talents/hero-bg.jpg',
			// categories come from content.json
		),
	),
	'expo'      => array(
		'title'    => 'Exposition & média',
		'slug'     => 'exposition-media',
		'slug_en'  => 'exhibition-media',
		'order'    => 4,
		'template' => 'templates/page-expo.php',
		'fields'   => array(
			'hero_title'       => 'Exposition & média',
			'hero_subtitle'    => "Des grilles du Sénat aux rives de la Méditerranée, l'objectif d'association donne à voir l'urgence et la beauté du monde marin",
			'hero_bg'          => 'assets/img/expo/hero-bg.jpg',
			'hero_floats'      => array( 'assets/img/expo/float-1.jpg', 'assets/img/expo/float-2.jpg', 'assets/img/expo/float-3.jpg', 'assets/img/expo/float-4.jpg' ),
			'hero_video_image' => 'assets/img/expo/video.jpg',
			'expo_title'       => "Nos médias, votre relais d'action",
			'expo_cta'         => 'Nous contacter',
			'expo_tag'         => 'Exposition · Paris, Nice',
			'expo_text'        => "À travers l'exposition itinérante « L'Océan Vivant », Wildlife Interactions fait vivre ses messages de sensibilisation au cœur de l'espace public. Accueillie par le Sénat sur les grilles du Jardin du Luxembourg, puis déclinée à Nice à l'occasion de l'UNOC 3, elle a rassemblé plusieurs millions de visiteurs. Ce voyage visuel, de l'équateur aux pôles, explore la splendeur des océans tout en sensibilisant le grand public aux enjeux environnementaux contemporains. Chaque image est accompagnée de légendes rédigées par les experts scientifiques de l'association.",
			'expo_media_1'     => array( 'image' => array( 'assets/img/expo/unoc.jpg', "Olivier Anrigo présentant l'exposition à des écoliers, UNOC, Nice" ), 'title' => 'UNOC', 'meta' => 'Nice, 2025', 'link' => '' ),
			'expo_media_2'     => array( 'image' => array( 'assets/img/expo/senat.jpg', "Inauguration de l'exposition au Sénat" ), 'poster' => array( 'assets/img/expo/poster.jpg', "Affiche « L'Océan Vivant », Grilles du Jardin du Luxembourg" ), 'title' => 'Grilles du Sénat', 'meta' => 'Paris, 2025', 'link' => '' ),
			'tv_tag'           => 'Sujet TV',
			'tv_text'          => "Les missions et les messages portés par Wildlife Interactions ont été relayés par plusieurs médias télévisés, désireux de porter à l'écran la beauté fragile du monde marin. Ces reportages permettent de faire connaître au grand public les expéditions scientifiques menées en Méditerranée, en Arctique et en Antarctique. En donnant la parole aux chercheurs et experts de l'association, ils contribuent à ancrer les enjeux climatiques dans le débat public. Chaque sujet TV devient ainsi un relais essentiel pour amplifier la mission de préservation de Wildlife Interactions.",
			'tv'               => array(
				array( 'image' => 'assets/img/expo/tv-pelagos.jpg', 'title' => 'Conférence Pelagos', 'meta' => 'Nice, 2025', 'link' => '' ),
				array( 'image' => 'assets/img/expo/tv-metier.jpg', 'title' => 'Métier de la Mer', 'meta' => '2025', 'link' => '' ),
				array( 'image' => 'assets/img/expo/tv-ulis.jpg', 'title' => 'ULIS Monaco', 'meta' => '2025', 'link' => '' ),
			),
			'mag_tag'          => 'Magazines',
			'mag_text'         => "L'engagement de Wildlife Interactions a trouvé sa place dans les pages de magazines de référence, tels que Géo et Héros. Ces publications offrent un espace narratif approfondi pour raconter les expéditions et les missions qui nourrissent le travail de l'association. À travers reportages et portfolios, elles permettent de toucher un lectorat curieux et engagé, sensible aux récits de terrain. Cette présence éditoriale renforce la portée du message porté collectivement par l'association et son réseau d'experts.",
			'magazines'        => array(
				array( 'name' => 'Géo magazine', 'cover' => array( 'assets/img/expo/cover-geo.jpg', 'Couverture GEO Hors-série « Beauté et enjeux des pôles »' ), 'pages' => array_fill( 0, 8, 'assets/img/expo/magazine-spread.jpg' ), 'download' => '' ),
				array( 'name' => 'Héros 8 magazine', 'cover' => array( 'assets/img/expo/cover-heros.jpg', "Couverture Héros n°8 « L'hymne à la mer »" ), 'pages' => array_fill( 0, 8, 'assets/img/expo/magazine-spread.jpg' ), 'download' => '' ),
			),
			'partners_title'   => 'Partenaires',
			'partners'         => array(
				array( 'logo' => 'assets/logos/partners/fondation-prince-albert.png', 'name' => 'Fondation Prince Albert II de Monaco' ),
				array( 'logo' => 'assets/logos/partners/gouvernement-princier.png', 'name' => 'Gouvernement Princier — Principauté de Monaco' ),
				array( 'logo' => 'assets/logos/partners/centre-scientifique.png', 'name' => 'Centre Scientifique de Monaco' ),
				array( 'logo' => 'assets/logos/partners/energy-observer.png', 'name' => 'Energy Observer Foundation' ),
				array( 'logo' => 'assets/logos/partners/polarx.png', 'name' => 'PolarX' ),
			),
		),
	),
	'contact'   => array(
		'title'    => 'Contact',
		'slug'     => 'contact',
		'slug_en'  => 'contact-us',
		'order'    => 5,
		'template' => 'templates/page-contact.php',
		'fields'   => array(
			'contact_title' => 'Comment pouvons-nous vous aider ?',
			'images'        => array( 'assets/img/contact/contact-1.jpg', 'assets/img/contact/contact-2.jpg', 'assets/img/contact/contact-3.jpg' ),
			'options'       => array(
				array( 'label' => 'J’ai une question', 'key' => 'question', 'intro' => 'Chaque question posée est une graine de curiosité qui nourrit notre mission. Notre équipe vous répond avec grand plaisir !' ),
				array( 'label' => 'Partenariat', 'key' => 'partenariat', 'intro' => "Rejoignez les entreprises et institutions qui, à nos côtés, protègent déjà plus de 87 500 km² d'écosystèmes marins essentiels au climat !" ),
				array( 'label' => 'Bénévolat', 'key' => 'benevolat', 'intro' => 'Ils sont déjà nombreux à donner de leur temps pour sensibiliser les jeunes générations. Rejoignez cette communauté engagée !' ),
			),
			'recipient'     => '',
		),
	),
);
