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

	$settings = get_option( 'fb_coming_soon_settings', [
		'status'  => 'off',
		'page_id' => 0,
		'mode'    => 'redirect',
	] );

	$status = $settings['status'] ?? 'off';

	// If disabled globally, don't show the toggle.
	if ( 'off' === $status ) {
		return;
	}

	$post_id  = defined( 'FB_ORIGINAL_POST_ID' ) ? FB_ORIGINAL_POST_ID : get_the_ID();

	// Determine if Coming Soon is currently active for this page.
	$is_active = false;
	if ( 'sitewide' === $status ) {
		$is_active = ( (int) get_the_ID() !== (int) ( $settings['page_id'] ?? 0 ) );
	} elseif ( 'per-page' === $status ) {
		$is_active = get_post_meta( $post_id, FB_COMING_SOON_META, true ) === '1';
	}

	$label = $is_active ? 'Coming Soon On' : 'Coming Soon Off';

	$url = wp_nonce_url(
		admin_url( 'admin-post.php?action=fb_toggle_coming_soon&post_id=' . $post_id ),
		'fb_toggle_coming_soon_' . $post_id
	);

	$wp_admin_bar->add_node( [
		'id'    => 'fb-coming-soon',
		'title' => ( $is_active ? '<span class="ab-icon dashicons-before dashicons-clock"></span>' : '' ) . '<span class="ab-label">' . $label . '</span>',
		'href'  => $url,
		'meta'  => [ 'class' => $is_active ? 'fb-cs-on' : 'fb-cs-off' ],
	] );
}, 100 );

/**
 * Admin bar colors for Coming Soon toggle.
 */
add_action( 'wp_head', function () {
	if ( ! is_admin_bar_showing() ) {
		return;
	}
	?>
	<style>
		#wpadminbar #wp-admin-bar-fb-coming-soon.fb-cs-on .ab-icon { color: #d63638 !important; }
	</style>
	<?php
} );

add_action( 'admin_head', function () {
	?>
	<style>
		#wpadminbar #wp-admin-bar-fb-coming-soon.fb-cs-on .ab-icon { color: #d63638 !important; }
	</style>
	<?php
} );

/**
 * Handle the admin bar toggle action.
 */
add_action( 'admin_post_fb_toggle_coming_soon', function () {
	$post_id = isset( $_GET['post_id'] ) ? (int) $_GET['post_id'] : 0;
	if ( ! $post_id || ! current_user_can( 'edit_post', $post_id ) ) {
		wp_die( 'Unauthorized', '', [ 'response' => 403 ] );
	}

	check_admin_referer( 'fb_toggle_coming_soon_' . $post_id );

	$settings = get_option( 'fb_coming_soon_settings', [
		'status'  => 'off',
		'page_id' => 0,
		'mode'    => 'redirect',
	] );
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
		// Currently Off: Default to Per-page when clicking the toggle on a specific page.
		$settings['status'] = 'per-page';
		update_option( 'fb_coming_soon_settings', $settings );
		update_post_meta( $post_id, FB_COMING_SOON_META, '1' );
	}

	wp_safe_redirect( get_permalink( $post_id ) );
	exit;
} );
