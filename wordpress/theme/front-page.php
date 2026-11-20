<?php
/**
 * Home — pinned hero: intro → Missions & Éducation list → Q&A (filled by js/activities.js).
 * Text: Pages › Accueil. Missions / programmes: menu « Missions & Éducation ».
 */

defined( 'ABSPATH' ) || exit;
get_header();
$id = get_queried_object_id();
?>
<main>
	<div class="hero-stage" data-hero-stage>
		<section class="hero" data-hero data-category="all" aria-labelledby="page-title"
			<?php if ( wi_img( wi_field( 'hero_bg', $id ) ) ) : ?>data-intro-bg="<?php echo esc_url( wi_img( wi_field( 'hero_bg', $id ) ) ); ?>"<?php endif; ?>
			data-intro-opacity="<?php echo esc_attr( (float) wi_field( 'hero_bg_opacity', $id, 0.35 ) ); ?>">
			<div class="hero__bgs" data-hero-bgs></div>

			<?php wi_site_header(); ?>

			<div class="hero__layer hero__layer--intro is-current" data-hero-layer="intro">
				<div class="hero__center">
					<h1 class="hero__title t-heading-h2-bold t-uppercase" id="page-title"><?php echo esc_html( wi_field( 'hero_title', $id, 'Wildlife Interactions' ) ); ?></h1>
					<?php if ( wi_field( 'hero_subtitle', $id ) ) : ?>
						<p class="hero__subtitle t-body-md-regular"><?php echo esc_html( wi_field( 'hero_subtitle', $id ) ); ?></p>
					<?php endif; ?>
					<?php $partners = wi_rows( 'partners', $id ); ?>
					<?php if ( $partners ) : ?>
						<ul class="partners hero__partners" aria-label="<?php echo esc_attr( wi_t( 'partners' ) ); ?>">
							<?php foreach ( $partners as $p ) : ?>
								<li><?php echo wi_img_tag( $p['logo'], array( 'alt' => $p['name'] ), 'medium' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>
				<?php $figures = wi_rows( 'figures', $id ); ?>
				<?php if ( $figures ) : ?>
					<div class="hero__data">
						<?php foreach ( $figures as $f ) : ?>
							<div class="data"><p class="data__value"><?php echo esc_html( $f['value'] ); ?></p><p class="data__label"><?php echo esc_html( $f['label'] ); ?></p></div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>

			<div class="hero__layer hero__layer--list" data-hero-layer="list" inert aria-hidden="true">
				<h2 class="visually-hidden"><?php echo esc_html( wi_t( 'listTitle' ) ); ?></h2>
				<ul class="activities" data-activities></ul>
			</div>

			<?php if ( wi_rows( 'faq', $id ) ) : ?>
				<div class="hero__layer hero__layer--faq" data-hero-layer="faq" inert aria-hidden="true">
					<h2 class="visually-hidden"><?php echo esc_html( wi_t( 'faqTitle' ) ); ?></h2>
					<div class="faq" data-faq></div>
				</div>
			<?php endif; ?>
		</section>
	</div>
</main>
<?php
get_footer();
