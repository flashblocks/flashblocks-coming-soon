<?php
/**
 * Custom columns in the admin post/page lists.
 *
 * @package flashblocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add "Coming Soon" column to public post types.
 */
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
