<?php
/**
 * Template Name: Missions / Éducation (liste + carte 3D)
 *
 * Content comes from the « Missions & Éducation » menu; this page only picks the category.
 */

defined( 'ABSPATH' ) || exit;
get_header();
$id  = get_queried_object_id();
$cat = wi_field( 'zone_category', $id, 'missions' );
?>
<main>
	<section class="hero" data-hero data-category="<?php echo esc_attr( $cat ); ?>" aria-labelledby="page-title">
		<div class="hero__bgs" data-hero-bgs></div>

		<?php wi_site_header(); ?>

		<div class="hero__layer hero__layer--list is-current" data-hero-layer="list">
			<h1 class="visually-hidden" id="page-title"><?php echo esc_html( get_the_title( $id ) ); ?></h1>
			<ul class="activities" data-activities></ul>
		</div>
	</section>
</main>
<?php
get_footer();
