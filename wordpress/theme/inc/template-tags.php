<?php
/**
 * Header (logo + main menu + « Nous soutenir »), footer and language switch.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Main menu items: [ url, label, category ('missions'|'education'|''), current ].
 * Uses the menu set in « Apparence › Menus » (location « Menu principal ») if there is one;
 * otherwise the theme's pages in a fixed order.
 */
function wi_menu_items() {
	$zone_pages = wi_zone_pages();
	$cat_of     = array();
	foreach ( $zone_pages as $cat => $page ) {
		$cat_of[ $page->ID ] = $cat;
	}
	$current = get_queried_object_id();
	$items   = array();

	$locations = get_nav_menu_locations();
	if ( ! empty( $locations['primary'] ) ) {
		foreach ( (array) wp_get_nav_menu_items( $locations['primary'] ) as $item ) {
			if ( $item->menu_item_parent ) {
				continue;
			}
			$id      = 'post_type' === $item->type ? (int) $item->object_id : 0;
			$items[] = array( $item->url, $item->title, isset( $cat_of[ $id ] ) ? $cat_of[ $id ] : '', $id && $id === $current );
		}
		return $items;
	}

	foreach ( array( 'missions', 'education' ) as $cat ) {
		if ( isset( $zone_pages[ $cat ] ) ) {
			$p       = $zone_pages[ $cat ];
			$items[] = array( get_permalink( $p ), get_the_title( $p ), $cat, $p->ID === $current );
		}
	}
	foreach ( array( 'templates/page-talents.php', 'templates/page-expo.php', 'templates/page-contact.php' ) as $tpl ) {
		foreach ( wi_pages_with_template( $tpl ) as $p ) {
			$items[] = array( get_permalink( $p ), get_the_title( $p ), '', $p->ID === $current );
			break;
		}
	}
	return $items;
}

/** Language links (Polylang), e.g. FR · EN */
function wi_language_switch( $tone = 'primary' ) {
	if ( ! function_exists( 'pll_the_languages' ) ) {
		return '';
	}
	$langs = pll_the_languages( array( 'raw' => 1, 'hide_if_empty' => 0, 'hide_if_no_translation' => 0 ) );
	if ( ! is_array( $langs ) || count( $langs ) < 2 ) {
		return '';
	}
	$out = '<ul class="lang-switch lang-switch--' . esc_attr( $tone ) . '" aria-label="' . esc_attr( wi_t( 'languages' ) ) . '">';
	foreach ( $langs as $lang ) {
		$out .= sprintf(
			'<li><a class="lang-switch__link" href="%s" hreflang="%s" lang="%s"%s>%s</a></li>',
			esc_url( $lang['url'] ),
			esc_attr( $lang['slug'] ),
			esc_attr( $lang['slug'] ),
			! empty( $lang['current_lang'] ) ? ' aria-current="true"' : '',
			esc_html( strtoupper( $lang['slug'] ) )
		);
	}
	return $out . '</ul>';
}

/**
 * Site header.
 * $args: tone 'primary' (on dark images) | 'secondary' (on light), class, attrs, dual_logo (logo swaps on light panels)
 */
function wi_site_header( $args = array() ) {
	$args = wp_parse_args( $args, array( 'tone' => 'primary', 'class' => '', 'attrs' => '', 'dual_logo' => false ) );
	$tone = 'secondary' === $args['tone'] ? 'secondary' : 'primary';
	?>
	<header class="site-header <?php echo esc_attr( $args['class'] ); ?>" data-site-header <?php echo $args['attrs']; // phpcs:ignore WordPress.Security.EscapeOutput -- fixed attributes from templates ?>>
		<a class="logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( wi_t( 'homeLabel' ) ); ?>">
			<?php if ( $args['dual_logo'] ) : ?>
				<img class="logo__img" src="<?php echo esc_url( wi_asset( 'assets/logos/logo-inverse.svg' ) ); ?>" width="63" height="68" alt="">
				<img class="logo__img logo__img--light" src="<?php echo esc_url( wi_asset( 'assets/logos/logo-default.svg' ) ); ?>" width="63" height="68" alt="">
			<?php else : ?>
				<img src="<?php echo esc_url( wi_asset( 'assets/logos/logo-inverse.svg' ) ); ?>" width="63" height="68" alt="">
			<?php endif; ?>
		</a>
		<button class="site-header__toggle" type="button" aria-expanded="false" aria-controls="site-nav" data-nav-toggle>
			<span class="site-header__toggle-bars" aria-hidden="true"></span>
			<span class="visually-hidden"><?php echo esc_html( wi_t( 'menu' ) ); ?></span>
		</button>
		<nav class="site-nav" id="site-nav" aria-label="<?php echo esc_attr( wi_t( 'mainNav' ) ); ?>">
			<ul class="site-nav__list">
				<?php foreach ( wi_menu_items() as $item ) : ?>
					<li><a class="menu-item menu-item--<?php echo esc_attr( $tone ); ?>" href="<?php echo esc_url( $item[0] ); ?>"<?php echo $item[2] ? ' data-menu-cat="' . esc_attr( $item[2] ) . '"' : ''; ?><?php echo $item[3] ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $item[1] ); ?></a></li>
				<?php endforeach; ?>
			</ul>
			<?php echo wi_language_switch( $tone ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in the function ?>
			<a class="cta cta--<?php echo esc_attr( $tone ); ?>" href="<?php echo esc_url( wi_contact_url( 'soutenir' ) ); ?>"><?php echo esc_html( wi_setting( 'support_label', 'Nous soutenir' ) ); ?></a>
		</nav>
	</header>
	<?php
}

/** Site footer (same on every page). */
function wi_site_footer() {
	?>
	<footer class="site-footer">
		<div class="site-footer__head">
			<div class="site-footer__title">
				<p class="site-footer__claim"><?php echo esc_html( wi_setting( 'footer_claim', 'Art. Éducation. Science' ) ); ?></p>
				<p class="site-footer__tagline"><?php echo esc_html( wi_setting( 'footer_tagline', "Ensemble, au service d'un impact durable pour la vie marine" ) ); ?></p>
			</div>
			<div class="site-footer__ctas">
				<a class="cta cta--secondary" href="<?php echo esc_url( wi_contact_url() ); ?>"><?php echo esc_html( wi_setting( 'contact_label', 'Contact' ) ); ?></a>
				<a class="cta cta--secondary-outline" href="<?php echo esc_url( wi_contact_url( 'soutenir' ) ); ?>"><?php echo esc_html( wi_setting( 'support_label', 'Nous soutenir' ) ); ?></a>
			</div>
		</div>
		<div class="site-footer__bottom">
			<a class="logo logo--sm" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( wi_t( 'homeLabel' ) ); ?>">
				<img src="<?php echo esc_url( wi_asset( 'assets/logos/logo-default.svg' ) ); ?>" width="63" height="68" alt="">
			</a>
			<p class="site-footer__copyright"><?php echo esc_html( wi_setting( 'footer_copyright', '© ' . gmdate( 'Y' ) . ' Wildlife Interactions. Tous droits réservés.' ) ); ?></p>
			<a class="site-footer__social" href="<?php echo esc_url( wi_setting( 'linkedin', 'https://www.linkedin.com/' ) ); ?>" target="_blank" rel="noopener" aria-label="LinkedIn">
				<span class="icon icon--32 icon--linkedin" aria-hidden="true"></span>
			</a>
		</div>
	</footer>
	<?php
}

/** <img> for an image field (empty string if none). */
function wi_img_tag( $image, $attrs = array(), $size = 'full' ) {
	$src = wi_img( $image, $size );
	if ( ! $src ) {
		return '';
	}
	if ( ! isset( $attrs['alt'] ) ) {
		$attrs['alt'] = wi_img_alt( $image );
	}
	$html = '<img src="' . esc_url( $src ) . '"';
	foreach ( $attrs as $k => $v ) {
		$html .= ' ' . $k . '="' . esc_attr( $v ) . '"';
	}
	return $html . '>';
}
