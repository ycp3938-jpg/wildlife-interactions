<?php
/**
 * Template Name: Exposition & média
 */

defined( 'ABSPATH' ) || exit;
get_header();
$id = get_queried_object_id();

/** One photo with caption (Figma: media). */
$wi_media = function ( $m, $class = 'media' ) {
	if ( empty( $m['image'] ) ) {
		return;
	}
	$open  = ! empty( $m['link'] ) ? '<a class="media__link" href="' . esc_url( $m['link'] ) . '" target="_blank" rel="noopener">' : '';
	$close = $open ? '</a>' : '';
	?>
	<figure class="<?php echo esc_attr( $class ); ?>">
		<?php echo $open; // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<?php if ( ! empty( $m['poster'] ) ) : ?>
			<div class="media__pair">
				<?php echo wi_img_tag( $m['image'], array( 'class' => 'media__img media__img--main', 'loading' => 'lazy' ), 'large' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<?php echo wi_img_tag( $m['poster'], array( 'class' => 'media__img media__img--poster', 'loading' => 'lazy' ), 'large' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</div>
		<?php else : ?>
			<?php echo wi_img_tag( $m['image'], array( 'class' => 'media__img', 'loading' => 'lazy' ), 'large' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<?php endif; ?>
		<?php echo $close; // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<figcaption class="media__caption"><span class="media__title"><?php echo esc_html( $m['title'] ); ?></span><span class="media__meta"><?php echo esc_html( $m['meta'] ); ?></span></figcaption>
	</figure>
	<?php
};
?>
<main>
	<section class="page-hero expo-hero" aria-labelledby="page-title">
		<?php echo wi_img_tag( wi_field( 'hero_bg', $id ), array( 'class' => 'page-hero__bg', 'alt' => '', 'aria-hidden' => 'true' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<?php wi_site_header(); ?>

		<div class="page-hero__content">
			<div class="page-hero__center">
				<h1 class="page-hero__title" id="page-title"><?php echo esc_html( wi_field( 'hero_title', $id, get_the_title( $id ) ) ); ?></h1>
				<?php if ( wi_field( 'hero_subtitle', $id ) ) : ?>
					<p class="page-hero__subtitle"><?php echo esc_html( wi_field( 'hero_subtitle', $id ) ); ?></p>
				<?php endif; ?>
			</div>
			<?php foreach ( array_slice( (array) wi_field( 'hero_floats', $id, array() ), 0, 4 ) as $i => $img ) : ?>
				<figure class="expo-hero__float expo-hero__float--<?php echo (int) $i + 1; ?>"><?php echo wi_img_tag( $img, array( 'alt' => '', 'loading' => 'lazy' ), 'medium_large' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></figure>
			<?php endforeach; ?>
			<?php if ( wi_field( 'hero_video_image', $id ) ) : ?>
				<figure class="expo-hero__float expo-hero__float--video">
					<?php if ( wi_field( 'hero_video_link', $id ) ) : ?><a href="<?php echo esc_url( wi_field( 'hero_video_link', $id ) ); ?>" target="_blank" rel="noopener"><?php endif; ?>
					<?php echo wi_img_tag( wi_field( 'hero_video_image', $id ), array(), 'large' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<?php if ( wi_field( 'hero_video_link', $id ) ) : ?></a><?php endif; ?>
				</figure>
			<?php endif; ?>
		</div>
	</section>

	<div class="expo">
		<section class="expo__section" aria-labelledby="expo-title">
			<div class="expo__head">
				<h2 class="expo__title" id="expo-title"><?php echo esc_html( wi_field( 'expo_title', $id ) ); ?></h2>
				<a class="cta cta--secondary" href="<?php echo esc_url( wi_contact_url() ); ?>"><?php echo esc_html( wi_field( 'expo_cta', $id, 'Nous contacter' ) ); ?></a>
			</div>
			<div class="expo__intro">
				<?php if ( wi_field( 'expo_tag', $id ) ) : ?><span class="tag tag--light"><?php echo esc_html( wi_field( 'expo_tag', $id ) ); ?></span><?php endif; ?>
				<p class="expo__intro-text"><?php echo esc_html( wi_field( 'expo_text', $id ) ); ?></p>
			</div>
			<div class="expo__media">
				<?php $wi_media( wi_field( 'expo_media_1', $id, array() ), 'media media--unoc' ); ?>
				<?php $wi_media( wi_field( 'expo_media_2', $id, array() ) ); ?>
			</div>
		</section>

		<?php if ( wi_rows( 'tv', $id ) || wi_field( 'tv_text', $id ) ) : ?>
			<section class="expo__section" aria-label="<?php echo esc_attr( wi_t( 'tvTopics' ) ); ?>">
				<div class="expo__intro">
					<span class="tag tag--light"><?php echo esc_html( wi_field( 'tv_tag', $id, 'Sujet TV' ) ); ?></span>
					<p class="expo__intro-text"><?php echo esc_html( wi_field( 'tv_text', $id ) ); ?></p>
				</div>
				<div class="expo__tv">
					<?php foreach ( wi_rows( 'tv', $id ) as $m ) { $wi_media( $m ); } ?>
				</div>
			</section>
		<?php endif; ?>

		<?php if ( wi_rows( 'magazines', $id ) || wi_field( 'mag_text', $id ) ) : ?>
			<section class="expo__section" aria-label="<?php echo esc_attr( wi_t( 'magazines' ) ); ?>">
				<div class="expo__intro">
					<span class="tag tag--light"><?php echo esc_html( wi_field( 'mag_tag', $id, 'Magazines' ) ); ?></span>
					<p class="expo__intro-text"><?php echo esc_html( wi_field( 'mag_text', $id ) ); ?></p>
				</div>
				<div class="expo__media">
					<?php foreach ( wi_rows( 'magazines', $id ) as $mag ) : ?>
						<?php $pages = is_array( $mag['pages'] ) ? $mag['pages'] : array(); ?>
						<div class="magazine">
							<figure class="magazine__cover"><?php echo wi_img_tag( $mag['cover'], array( 'loading' => 'lazy' ), 'large' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><figcaption><?php echo esc_html( $mag['name'] ); ?></figcaption></figure>
							<?php if ( $pages ) : ?>
								<div class="carousel" data-carousel tabindex="0" aria-roledescription="carrousel" aria-label="<?php echo esc_attr( wi_t( 'pages' ) . ' — ' . $mag['name'] ); ?>">
									<div class="carousel__viewport"><div class="carousel__track" data-carousel-track>
										<?php foreach ( $pages as $n => $img ) : ?>
											<div class="carousel__slide"><?php echo wi_img_tag( $img, array( 'alt' => $mag['name'] . ' — ' . wi_t( 'page' ) . ' ' . ( $n + 1 ), 'draggable' => 'false', 'loading' => 'lazy' ), 'large' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
										<?php endforeach; ?>
									</div></div>
									<div class="carousel__nav">
										<div class="carousel__dots" data-carousel-dots></div>
										<div class="carousel__ctas">
											<button class="cta cta--secondary cta--icon" type="button" data-carousel-prev aria-label="<?php echo esc_attr( wi_t( 'pagePrev' ) ); ?>"><span class="icon icon--arrow-left" aria-hidden="true"></span></button>
											<button class="cta cta--secondary cta--icon" type="button" data-carousel-next aria-label="<?php echo esc_attr( wi_t( 'pageNext' ) ); ?>"><span class="icon icon--arrow-right" aria-hidden="true"></span></button>
											<?php if ( ! empty( $mag['download'] ) ) : ?>
												<a class="cta cta--secondary" href="<?php echo esc_url( $mag['download'] ); ?>" download><?php echo esc_html( wi_t( 'download' ) ); ?><span class="icon icon--download" aria-hidden="true"></span></a>
											<?php endif; ?>
										</div>
									</div>
								</div>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</section>
		<?php endif; ?>

		<?php if ( wi_rows( 'partners', $id ) ) : ?>
			<section class="expo__section partners-band" aria-labelledby="partners-title">
				<h2 class="partners-band__title" id="partners-title"><?php echo esc_html( wi_field( 'partners_title', $id, wi_t( 'partners' ) ) ); ?></h2>
				<div class="partners-band__logos">
					<?php foreach ( wi_rows( 'partners', $id ) as $p ) : ?>
						<?php echo wi_img_tag( $p['logo'], array( 'alt' => $p['name'], 'loading' => 'lazy' ), 'medium' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<?php endforeach; ?>
				</div>
			</section>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();
