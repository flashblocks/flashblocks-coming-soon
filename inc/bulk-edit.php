<?php
/**
 * Bulk edit actions for Coming Soon toggle.
 *
 * @package flashblocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_init', function () {
	foreach ( get_post_types( [ 'public' => true ] ) as $post_type ) {
		$screen = 'edit-' . $post_type;

		add_filter( "bulk_actions-{$screen}", function ( $actions ) {
			$actions['fb_coming_soon_on']  = 'Coming Soon: On';
			$actions['fb_coming_soon_off'] = 'Coming Soon: Off';
			return $actions;
		} );

		add_filter( "handle_bulk_actions-{$screen}", function ( $redirect_to, $action, $post_ids ) {
			if ( ! in_array( $action, [ 'fb_coming_soon_on', 'fb_coming_soon_off' ], true ) ) {
				return $redirect_to;
			}

			$value = $action === 'fb_coming_soon_on' ? '1' : '0';

			foreach ( $post_ids as $post_id ) {
				if ( current_user_can( 'edit_post', $post_id ) ) {
					update_post_meta( (int) $post_id, FB_COMING_SOON_META, $value );
				}
			}

			return add_query_arg( 'fb_cs_updated', count( $post_ids ), $redirect_to );
		}, 10, 3 );
	}
} );

/**
 * Show admin notice after bulk update.
 */
add_action( 'admin_notices', function () {
	if ( ! isset( $_GET['fb_cs_updated'] ) ) {
		return;
	}

	$count = (int) $_GET['fb_cs_updated'];
	printf(
		'<div class="notice notice-success is-dismissible"><p>Coming Soon updated for %d %s.</p></div>',
		$count,
		_n( 'item', 'items', $count )
	);
} );
