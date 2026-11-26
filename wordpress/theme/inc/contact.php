<?php
/**
 * Contact form (templates/page-contact.php + js/pages.js) → e-mail.
 * Tip for IONOS: install an SMTP extension (e.g. « WP Mail SMTP ») so the
 * messages are sent from your IONOS mailbox and don't land in spam.
 */

defined( 'ABSPATH' ) || exit;

function wi_contact_submit() {
	if ( ! check_ajax_referer( 'wi_contact', 'nonce', false ) ) {
		wp_send_json_error( 'nonce', 403 );
	}
	// Spam trap: humans never fill the hidden « website » field.
	if ( ! empty( $_POST['website'] ) ) {
		wp_send_json_success();
	}
	// One message per visitor every 30 seconds.
	$ip  = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$key = 'wi_contact_' . md5( $ip );
	if ( get_transient( $key ) ) {
		wp_send_json_error( 'too_fast', 429 );
	}

	$get = function ( $name, $multiline = false ) {
		$value = isset( $_POST[ $name ] ) ? wp_unslash( $_POST[ $name ] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		return $multiline ? sanitize_textarea_field( $value ) : sanitize_text_field( $value );
	};
	$d = array(
		'subject'   => $get( 'subject' ),
		'firstname' => $get( 'firstname' ),
		'lastname'  => $get( 'lastname' ),
		'email'     => sanitize_email( $get( 'email' ) ),
		'phone'     => $get( 'phone' ),
		'company'   => $get( 'company' ),
		'message'   => $get( 'message', true ),
	);
	if ( ! $d['firstname'] || ! $d['lastname'] || ! is_email( $d['email'] ) || ! $d['message'] ) {
		wp_send_json_error( 'invalid', 400 );
	}

	$page_id = isset( $_POST['page_id'] ) ? absint( $_POST['page_id'] ) : 0;
	$to      = $page_id ? wi_field( 'recipient', $page_id ) : '';
	if ( ! is_email( $to ) ) {
		$to = wi_setting( 'email', get_option( 'admin_email' ) );
	}

	$lines = array(
		'Objet : ' . $d['subject'],
		'Prénom : ' . $d['firstname'],
		'Nom : ' . $d['lastname'],
		'E-mail : ' . $d['email'],
	);
	if ( $d['phone'] ) {
		$lines[] = 'Téléphone : ' . $d['phone'];
	}
	if ( $d['company'] ) {
		$lines[] = 'Entreprise : ' . $d['company'];
	}
	$lines[] = '';
	$lines[] = $d['message'];

	$sent = wp_mail(
		$to,
		sprintf( '[%s] %s %s', $d['subject'] ? $d['subject'] : 'Contact', $d['firstname'], $d['lastname'] ),
		implode( "\n", $lines ),
		array( 'Reply-To: ' . $d['firstname'] . ' ' . $d['lastname'] . ' <' . $d['email'] . '>' )
	);
	if ( ! $sent ) {
		wp_send_json_error( 'mail', 500 );
	}
	set_transient( $key, 1, 30 );
	wp_send_json_success();
}
add_action( 'wp_ajax_wi_contact', 'wi_contact_submit' );
add_action( 'wp_ajax_nopriv_wi_contact', 'wi_contact_submit' );
