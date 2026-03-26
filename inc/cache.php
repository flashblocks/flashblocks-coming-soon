<?php
/**
 * Cache clearing logic for Flashblocks Coming Soon.
 *
 * @package flashblocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Clear caches when the coming soon meta is updated.
 */
function fb_clear_coming_soon_cache( $meta_id, $post_id, $meta_key ) {
	if ( FB_COMING_SOON_META !== $meta_key ) {
		return;
	}

	// Internal WP cache.
	clean_post_cache( $post_id );

	// Support for WP Rocket if active.
	if ( function_exists( 'rocket_clean_post' ) ) {
		rocket_clean_post( $post_id );
	}

	// Support for Autoptimize if active.
	if ( class_exists( 'autoptimizeCache' ) ) {
		\autoptimizeCache::clearall();
	}

	// Support for Kinsta if active.
	global $KinstaCache;
	if ( ! empty( $KinstaCache ) && isset( $KinstaCache->kinsta_cache_purge ) ) {
		$KinstaCache->kinsta_cache_purge->initiate_purge( $post_id );
	}
}
add_action( 'added_post_meta', 'fb_clear_coming_soon_cache', 10, 3 );
add_action( 'updated_post_meta', 'fb_clear_coming_soon_cache', 10, 3 );
add_action( 'deleted_post_meta', 'fb_clear_coming_soon_cache', 10, 3 );

/**
 * Clear global cache when settings are updated.
 */
add_action( 'update_option_fb_coming_soon_settings', function ( $old_value, $new_value ) {
	// If it's the first time saving, old_value might be false.
	$old = is_array( $old_value ) ? $old_value : [];
	$new = is_array( $new_value ) ? $new_value : [];

	$old_status = $old['status'] ?? '';
	$new_status = $new['status'] ?? '';
	$old_page   = (int) ( $old['page_id'] ?? 0 );
	$new_page   = (int) ( $new['page_id'] ?? 0 );

	// If the status or page_id changed, clear everything.
	if ( $old_status !== $new_status || $old_page !== $new_page ) {
		global $KinstaCache;
		if ( ! empty( $KinstaCache ) && isset( $KinstaCache->kinsta_cache_purge ) ) {
			$KinstaCache->kinsta_cache_purge->initiate_purge();
		}

		if ( class_exists( 'autoptimizeCache' ) ) {
			\autoptimizeCache::clearall();
		}
	}
}, 10, 2 );
