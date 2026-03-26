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

	$post_id = defined( 'FB_ORIGINAL_POST_ID' ) ? FB_ORIGINAL_POST_ID : get_the_ID();
	$is_on   = get_post_meta( $post_id, FB_COMING_SOON_META, true ) === '1';
	$toggle  = $is_on ? '0' : '1';

	$status_color = $is_on ? '#d63638' : '#72aee6';
	$icon = $is_on ? 'dashicons-hidden' : 'dashicons-visibility';
	$label = $is_on ? 'Coming Soon On' : 'Coming Soon Off';

	$url = wp_nonce_url(
		admin_url( 'admin-post.php?action=fb_toggle_coming_soon&post_id=' . $post_id . '&value=' . $toggle ),
		'fb_toggle_coming_soon_' . $post_id
	);

	$wp_admin_bar->add_node( [
		'id'    => 'fb-coming-soon',
		'title' => '<span class="ab-icon dashicons-before ' . $icon . '" style="color:' . $status_color . '; margin-top: 2px !important;"></span><span class="ab-label">' . $label . '</span>',
		'href'  => $url,
	] );
}, 100 );

/**
 * Handle the admin bar toggle action.
 */
add_action( 'admin_post_fb_toggle_coming_soon', function () {
	$post_id = isset( $_GET['post_id'] ) ? (int) $_GET['post_id'] : 0;
	$value   = isset( $_GET['value'] ) && $_GET['value'] === '1' ? '1' : '0';

	if ( ! $post_id || ! current_user_can( 'edit_post', $post_id ) ) {
		wp_die( 'Unauthorized', '', [ 'response' => 403 ] );
	}

	check_admin_referer( 'fb_toggle_coming_soon_' . $post_id );

	update_post_meta( $post_id, FB_COMING_SOON_META, $value );

	wp_safe_redirect( get_permalink( $post_id ) );
	exit;
} );
