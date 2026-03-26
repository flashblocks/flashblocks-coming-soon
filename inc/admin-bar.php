<?php
/**
 * Admin bar menu and toggle action handler.
 *
 * @package flashblocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add "Coming Soon" toggle button to the admin bar on the front end.
 */
add_action( 'admin_bar_menu', function ( WP_Admin_Bar $wp_admin_bar ) {
	if ( is_admin() || ! is_singular() || ! current_user_can( 'edit_posts' ) ) {
		return;
	}

	$settings = get_option( 'fb_coming_soon_settings' );
	$status   = $settings['status'] ?? 'off';
	$post_id  = defined( 'FB_ORIGINAL_POST_ID' ) ? FB_ORIGINAL_POST_ID : get_the_ID();

	// Determine if Coming Soon is currently active for this page.
	$is_active = false;
	if ( 'sitewide' === $status ) {
		$is_active = ( (int) get_the_ID() !== (int) ( $settings['page_id'] ?? 0 ) );
	} elseif ( 'per-page' === $status ) {
		$is_active = get_post_meta( $post_id, FB_COMING_SOON_META, true ) === '1';
	}

	$label = $is_active ? 'Coming Soon On' : 'Coming Soon Off';
	$icon  = $is_active ? 'dashicons-hidden' : 'dashicons-visibility';
	$color = $is_active ? '#d63638' : '#72aee6';

	// The toggle action depends on the current mode.
	$url = wp_nonce_url(
		admin_url( 'admin-post.php?action=fb_toggle_coming_soon&post_id=' . $post_id ),
		'fb_toggle_coming_soon_' . $post_id
	);

	$wp_admin_bar->add_node( [
		'id'    => 'fb-coming-soon',
		'title' => '<span class="ab-icon dashicons-before ' . $icon . '" style="color:' . $color . '; margin-top: 2px !important;"></span><span class="ab-label">' . $label . '</span>',
		'href'  => $url,
	] );
}, 100 );

/**
 * Handle the admin bar toggle action.
 */
add_action( 'admin_post_fb_toggle_coming_soon', function () {
	$post_id = isset( $_GET['post_id'] ) ? (int) $_GET['post_id'] : 0;
	if ( ! $post_id || ! current_user_can( 'edit_post', $post_id ) ) {
		wp_die( 'Unauthorized', '', [ 'response' => 403 ] );
	}

	check_admin_referer( 'fb_toggle_coming_soon_' . $post_id );

	$settings = get_option( 'fb_coming_soon_settings' );
	$status   = $settings['status'] ?? 'off';

	if ( 'sitewide' === $status ) {
		// Toggle global status off.
		$settings['status'] = 'off';
		update_option( 'fb_coming_soon_settings', $settings );
	} elseif ( 'per-page' === $status ) {
		// Toggle post meta.
		$is_on = get_post_meta( $post_id, FB_COMING_SOON_META, true ) === '1';
		update_post_meta( $post_id, FB_COMING_SOON_META, $is_on ? '0' : '1' );
	} else {
		// Currently Off: Default to Site-wide or Per-page?
		// Let's default to Sitewide if they click it from Off.
		$settings['status'] = 'sitewide';
		update_option( 'fb_coming_soon_settings', $settings );
	}

	wp_safe_redirect( get_permalink( $post_id ) );
	exit;
} );
