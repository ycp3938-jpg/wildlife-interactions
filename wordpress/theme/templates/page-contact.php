<?php
/**
 * Template Name: Contact
 *
 * The form is sent by e-mail (inc/contact.php) to the address set on this page,
 * or the site contact e-mail.
 */

defined( 'ABSPATH' ) || exit;
get_header();
$id      = get_queried_object_id();
$email   = wi_setting( 'email', get_option( 'admin_email' ) );
$options = wi_rows( 'options', $id );
if ( ! $options ) {
	$options = array( array( 'key' => 'question', 'label' => 'J’ai une question', 'intro' => '' ) );
}
?>
<main>
	<section class="contact" aria-labelledby="page-title">
		<?php wi_site_header( array( 'class' => 'site-header--split', 'tone' => 'secondary', 'dual_logo' => true ) ); ?>

		<div class="image-anim" data-image-anim>
			<?php foreach ( (array) wi_field( 'images', $id, array() ) as $img ) : ?>
				<?php echo wi_img_tag( $img, array( 'class' => 'image-anim__img', 'alt' => '' ), 'large' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<?php endforeach; ?>
			<div class="image-anim__contact">
				<a class="image-anim__mail" href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a>
				<a href="<?php echo esc_url( wi_setting( 'linkedin', 'https://www.linkedin.com/' ) ); ?>" target="_blank" rel="noopener" aria-label="LinkedIn"><span class="icon icon--32 icon--linkedin" aria-hidden="true"></span></a>
			</div>
		</div>

		<div class="contact__main">
			<div class="contact__top">
				<h1 class="contact__title" id="page-title"><?php echo esc_html( wi_field( 'contact_title', $id, get_the_title( $id ) ) ); ?></h1>
				<div class="contact__options">
					<div class="contact__chips" role="group" aria-label="<?php echo esc_attr( wi_t( 'contactSubject' ) ); ?>">
						<?php foreach ( $options as $i => $o ) : ?>
							<button class="cta <?php echo 0 === $i ? 'cta--secondary' : 'cta--secondary-outline'; ?>" type="button" data-contact-option="<?php echo esc_attr( sanitize_title( $o['key'] ? $o['key'] : $o['label'] ) ); ?>" aria-pressed="<?php echo 0 === $i ? 'true' : 'false'; ?>" data-intro="<?php echo esc_attr( $o['intro'] ); ?>"><?php echo esc_html( $o['label'] ); ?></button>
						<?php endforeach; ?>
					</div>
					<p class="contact__intro" data-contact-intro aria-live="polite"></p>
				</div>
			</div>

			<form class="contact-form" data-contact-form novalidate>
				<input type="hidden" name="subject" value="">
				<input type="hidden" name="page_id" value="<?php echo (int) $id; ?>">
				<div class="contact-form__hp" aria-hidden="true"><label>Website <input name="website" tabindex="-1" autocomplete="off"></label></div>
				<?php
				$fields = array(
					array( 'firstname', 'text', 'given-name', true ),
					array( 'lastname', 'text', 'family-name', true ),
					array( 'email', 'email', 'email', true ),
					array( 'phone', 'tel', 'tel', false ),
					array( 'company', 'text', 'organization', false ),
				);
				foreach ( $fields as $f ) :
					?>
					<label class="field"><span class="visually-hidden"><?php echo esc_html( wi_t( $f[0] ) ); ?></span><input class="field__input" type="<?php echo esc_attr( $f[1] ); ?>" name="<?php echo esc_attr( $f[0] ); ?>" placeholder="<?php echo esc_attr( wi_t( $f[0] ) ); ?>" autocomplete="<?php echo esc_attr( $f[2] ); ?>"<?php echo $f[3] ? ' required' : ''; ?>></label>
				<?php endforeach; ?>
				<label class="field"><span class="visually-hidden"><?php echo esc_html( wi_t( 'message' ) ); ?></span><textarea class="field__input" name="message" placeholder="<?php echo esc_attr( wi_t( 'message' ) ); ?>" rows="1" required></textarea></label>
				<button class="cta cta--secondary" type="submit"><?php echo esc_html( wi_t( 'send' ) ); ?></button>
				<p class="contact-form__status" data-form-status aria-live="polite"></p>
			</form>
		</div>
	</section>
</main>
<?php
get_footer();
