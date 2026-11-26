<?php
/**
 * Template Name: Missions / Éducation (liste + carte 3D)
 *
 * Content comes from the « Missions & Éducation » menu; this page only picks the category.
 * Column of thumbnails on the left: hover shows a card's photo and name (bottom right),
 * a click opens its detail page (js/activities.js).
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
			<ul class="rail" data-rail data-mode="hover"></ul>
			<div class="rail-info rail-info--corner" data-rail-info aria-live="polite">
				<p class="rail-info__name t-heading-h2-bold t-uppercase" data-rail-title></p>
				<div class="rail-info__toggle" data-view-toggle-slot></div>
			</div>
		</div>
	</section>
	</div>
</main>
<?php
get_footer();
