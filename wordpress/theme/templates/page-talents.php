<?php
/**
 * Template Name: Talents
 *
 * Horizontal scroll: hero (5 cards) then all the people (filled by js/pages.js from the « Talents » menu).
 */

defined( 'ABSPATH' ) || exit;
get_header();
$id = get_queried_object_id();
?>
<main>
	<div class="h-stage" data-h-stage>
		<div class="h-stage__sticky">
			<?php wi_site_header( array( 'class' => 'h-stage__header', 'attrs' => 'data-h-header', 'dual_logo' => true ) ); ?>
			<div class="h-stage__track" data-h-track>
				<section class="page-hero" aria-labelledby="page-title">
					<?php echo wi_img_tag( wi_field( 'hero_bg', $id ), array( 'class' => 'page-hero__bg', 'alt' => '', 'aria-hidden' => 'true' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<div class="page-hero__content">
						<div class="page-hero__center">
							<h1 class="page-hero__title" id="page-title"><?php echo esc_html( wi_field( 'hero_title', $id, get_the_title( $id ) ) ); ?></h1>
							<?php if ( wi_field( 'hero_subtitle', $id ) ) : ?>
								<p class="page-hero__subtitle"><?php echo esc_html( wi_field( 'hero_subtitle', $id ) ); ?></p>
							<?php endif; ?>
						</div>
						<ul class="talent-cats" data-talent-categories aria-label="<?php echo esc_attr( wi_t( 'talentDomains' ) ); ?>"></ul>
					</div>
				</section>

				<section class="profiles" aria-labelledby="profiles-title">
					<h2 class="visually-hidden" id="profiles-title"><?php echo esc_html( wi_t( 'ourTalents' ) ); ?></h2>
					<ul class="profiles__grid" data-profiles></ul>
				</section>
			</div>
		</div>
	</div>
</main>
<?php
get_footer();
