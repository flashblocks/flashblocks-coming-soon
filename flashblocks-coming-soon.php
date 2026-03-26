<?php
/**
 * Plugin Name:  Flashblocks Coming Soon
 * Description:  Per-page coming soon toggle via post meta. Redirects to a coming-soon page when active.
 * Version:      1.0.0
 * Requires at least: 6.7
 * Requires PHP: 7.4
 * Author:       Sunny Morgan
 * License:      GPL-2.0-or-later
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:  flashblocks-coming-soon
 *
 * @package flashblocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'FB_COMING_SOON_META', '_fb_coming_soon' );

// ---------------------------------------------------------------------------
// Meta registration
// ---------------------------------------------------------------------------

add_action( 'init', function () {
	foreach ( get_post_types( [ 'public' => true ] ) as $post_type ) {
		register_post_meta( $post_type, FB_COMING_SOON_META, [
			'show_in_rest'  => true,
			'single'        => true,
			'type'          => 'string',
			'default'       => '0',
			'auth_callback' => function () {
				return current_user_can( 'edit_posts' );
			},
		] );
	}
} );

// ---------------------------------------------------------------------------
// Preview cookie
// ---------------------------------------------------------------------------

add_action( 'template_redirect', function () {
	if ( isset( $_GET['preview-on'] ) ) {
		setcookie( 'fb_preview_mode', '1', time() + MONTH_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN, is_ssl() );
		$_COOKIE['fb_preview_mode'] = '1';
	}
}, 1 );

// ---------------------------------------------------------------------------
// Redirect
// ---------------------------------------------------------------------------

add_action( 'template_redirect', function () {
	if (
		is_user_logged_in() || is_admin() || wp_doing_ajax() || wp_doing_cron()
		|| defined( 'REST_REQUEST' ) || defined( 'XMLRPC_REQUEST' )
		|| isset( $_GET['preview'] ) || is_404()
		|| isset( $_COOKIE['fb_preview_mode'] )
		|| ! is_singular()
	) {
		return;
	}

	if ( get_post_meta( get_the_ID(), FB_COMING_SOON_META, true ) !== '1' ) {
		return;
	}

	$coming_soon_page = get_page_by_path( 'coming-soon' );

	if ( $coming_soon_page ) {
		if ( ! is_page( 'coming-soon' ) ) {
			wp_redirect( get_permalink( $coming_soon_page ), 302 );
			exit;
		}
	} else {
		global $wp_query;
		$wp_query->set_404();
		status_header( 404 );
		nocache_headers();
		include get_404_template();
		exit;
	}
} );

// ---------------------------------------------------------------------------
// Admin bar toggle (front end only)
// ---------------------------------------------------------------------------

add_action( 'admin_bar_menu', function ( WP_Admin_Bar $wp_admin_bar ) {
	if ( is_admin() || ! is_singular() || ! current_user_can( 'edit_posts' ) ) {
		return;
	}

	$post   = get_post();
	$is_on  = get_post_meta( $post->ID, FB_COMING_SOON_META, true ) === '1';
	$label  = $is_on ? 'Coming Soon On' : 'Coming Soon Off';
	$toggle = $is_on ? '0' : '1';

	$url = wp_nonce_url(
		admin_url( 'admin-post.php?action=fb_toggle_coming_soon&post_id=' . $post->ID . '&value=' . $toggle ),
		'fb_toggle_coming_soon_' . $post->ID
	);

	$wp_admin_bar->add_node( [
		'id'    => 'fb-coming-soon',
		'title' => $label,
		'href'  => $url,
	] );
}, 100 );

// ---------------------------------------------------------------------------
// Handle admin bar toggle action
// ---------------------------------------------------------------------------

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

// ---------------------------------------------------------------------------
// Block editor sidebar panel
// ---------------------------------------------------------------------------

add_action( 'enqueue_block_editor_assets', function () {
	$asset_file = __DIR__ . '/build/index.asset.php';

	if ( ! file_exists( $asset_file ) ) {
		return;
	}

	$asset = require $asset_file;

	wp_enqueue_script(
		'flashblocks-coming-soon-editor',
		plugins_url( 'build/index.js', __FILE__ ),
		$asset['dependencies'],
		$asset['version']
	);
} );

// ---------------------------------------------------------------------------
// Admin columns
// ---------------------------------------------------------------------------

add_action( 'admin_init', function () {
	foreach ( get_post_types( [ 'public' => true ] ) as $post_type ) {
		$col_hook    = $post_type === 'page' ? 'manage_pages_columns' : "manage_{$post_type}_posts_columns";
		$custom_hook = "manage_{$post_type}_posts_custom_column";

		add_filter( $col_hook, function ( $columns ) {
			$columns['fb_coming_soon'] = 'Coming Soon';
			return $columns;
		} );

		add_action( $custom_hook, function ( $column, $post_id ) {
			if ( $column === 'fb_coming_soon' ) {
				echo get_post_meta( $post_id, FB_COMING_SOON_META, true ) === '1' ? '🔒 On' : '—';
			}
		}, 10, 2 );
	}
} );

// ---------------------------------------------------------------------------
// One-time migration: comment_status 'open' → _fb_coming_soon = '1'
// ---------------------------------------------------------------------------

add_action( 'admin_init', function () {
	if ( get_option( '_fb_coming_soon_migrated' ) ) {
		return;
	}

	$posts = get_posts( [
		'post_type'      => get_post_types( [ 'public' => true ] ),
		'post_status'    => 'any',
		'comment_status' => 'open',
		'numberposts'    => -1,
		'fields'         => 'ids',
	] );

	foreach ( $posts as $post_id ) {
		update_post_meta( $post_id, FB_COMING_SOON_META, '1' );
		wp_update_post( [
			'ID'             => $post_id,
			'comment_status' => 'closed',
		] );
	}

	update_option( '_fb_coming_soon_migrated', true );
} );
