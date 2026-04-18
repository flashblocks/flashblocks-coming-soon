<?php
/**
 * Cookie-based preview mode logic.
 *
 * @package flashblocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle the ?preview-on query parameter to set a long-lived preview cookie.
 */
add_action( 'template_redirect', function () {
	if ( isset( $_GET['preview-on'] ) ) {
		setcookie( 'fb_preview_mode', '1', time() + MONTH_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true );
		$_COOKIE['fb_preview_mode'] = '1';
	}

	if ( isset( $_GET['preview-off'] ) ) {
		setcookie( 'fb_preview_mode', '', time() - HOUR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true );
		unset( $_COOKIE['fb_preview_mode'] );
	}
}, 1 );
