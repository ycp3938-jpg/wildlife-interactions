<?php
/**
 * Fallback template (blog posts, archives, pages without a special template):
 * simple text page in the site's style.
 */

defined( 'ABSPATH' ) || exit;
get_header();
?>
<main class="wi-text-page">
	<div class="wi-text-page__head">
		<?php wi_site_header(); ?>
	</div>
	<div class="wi-text-page__body">
		<?php if ( have_posts() ) : ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<article <?php post_class( 'wi-text-page__article' ); ?>>
					<?php if ( is_singular() ) : ?>
						<h1 class="wi-text-page__title"><?php the_title(); ?></h1>
						<div class="wi-text-page__content"><?php the_content(); ?></div>
					<?php else : ?>
						<h2 class="wi-text-page__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<div class="wi-text-page__content"><?php the_excerpt(); ?></div>
					<?php endif; ?>
				</article>
			<?php endwhile; ?>
			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<h1 class="wi-text-page__title">404</h1>
			<p><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html( wi_t( 'homeLabel' ) ); ?></a></p>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();
