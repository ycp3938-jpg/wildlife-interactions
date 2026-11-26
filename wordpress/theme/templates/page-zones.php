<?php
/**
 * Template Name: Missions / Éducation (liste + carte 3D)
 *
 * Content comes from the « Missions & Éducation » menu; this page only picks the category.
 * Row of thumbnails along the top, slid by the scroll (js/activities.js), like the Home.
 */

defined( 'ABSPATH' ) || exit;
get_header();
$id  = get_queried_object_id();
$cat = wi_field( 'zone_category', $id, 'missions' );
?>
<main>
	<div class="hero-stage" data-hero-stage>
	<section class="hero" data-hero data-category="<?php echo esc_attr( $cat ); ?>" aria-labelledby="page-title">
		<div class="hero__bgs" data-hero-bgs></div>

		<?php wi_site_header(); ?>

		<div class="hero__layer hero__layer--list hero__layer--rail is-current" data-hero-layer="list">
			<h1 class="visually-hidden" id="page-title"><?php echo esc_html( get_the_title( $id ) ); ?></h1>
			<ul class="rail" data-rail></ul>
			<div class="rail-info" data-rail-info aria-live="polite">
				<div class="rail-info__row">
					<span class="rail-info__tag" data-rail-tag></span>
					<p class="rail-info__title" data-rail-title></p>
					<button class="cta cta--primary rail-info__more" type="button" data-rail-more aria-haspopup="dialog"><?php echo esc_html( wi_t( 'more' ) ); ?></button>
				</div>
				<div class="rail-info__toggle" data-view-toggle-slot></div>
			</div>
		</div>
	</section>
	</div>
</main>
<?php
get_footer();
