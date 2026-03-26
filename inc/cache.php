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
