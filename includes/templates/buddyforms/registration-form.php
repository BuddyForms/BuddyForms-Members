<?php

/**
 * Auth form login
 *
 * This template can be overridden by copying it to yourtheme/buddyforms/form-login.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( is_user_logged_in() ) {
	return;
}

$buddyforms_registration_form = get_option( 'buddyforms_registration_form' );

echo do_shortcode( '[bf form_slug="' . $buddyforms_registration_form . '"]' );

?>


