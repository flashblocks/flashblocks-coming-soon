<?php
/**
 * Legacy migration tool (comment_status -> post_meta).
 *
 * @package flashblocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Triggered by ?fb_run_migration=1 in the admin.
 */
add_action( 'admin_init', function () {
	if ( ! isset( $_GET['fb_run_migration'] ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( get_option( '_fb_coming_soon_migrated' ) ) {
		wp_die( 'Migration already ran — nothing to do.' );
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

	wp_die( 'Migration complete. ' . count( $posts ) . ' post(s) updated.' );
} );
