<?php
/**
 * Meta registration and Block Editor integration.
 *
 * @package flashblocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register post meta for all public post types.
 */
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

/**
 * Enqueue editor assets for the Coming Soon sidebar panel.
 */
add_action( 'enqueue_block_editor_assets', function () {
	$asset_file = dirname( __DIR__ ) . '/build/index.asset.php';

	if ( ! file_exists( $asset_file ) ) {
		return;
	}

	$asset = require $asset_file;

	wp_enqueue_script(
		'flashblocks-coming-soon-editor',
		plugins_url( 'build/index.js', dirname( __FILE__ ) ),
		$asset['dependencies'],
		$asset['version']
	);

	wp_localize_script( 'flashblocks-coming-soon-editor', 'fbComingSoon', [
		'settings' => get_option( 'fb_coming_soon_settings', [
			'status'  => 'off',
			'page_id' => 0,
			'mode'    => 'redirect',
		] ),
	] );
} );
