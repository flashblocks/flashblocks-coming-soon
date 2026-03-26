<?php
/**
 * Core Coming Soon logic (Redirect or Replace).
 *
 * @package flashblocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'template_redirect', function () {
	if (
		is_user_logged_in() || is_admin() || wp_doing_ajax() || wp_doing_cron()
		|| defined( 'REST_REQUEST' ) || defined( 'XMLRPC_REQUEST' )
		|| isset( $_GET['preview'] ) || isset( $_GET['preview-on'] ) || is_404()
		|| isset( $_COOKIE['fb_preview_mode'] )
		|| ! is_singular()
	) {
		return;
	}

	if ( get_post_meta( get_the_ID(), FB_COMING_SOON_META, true ) !== '1' ) {
		return;
	}

	$coming_soon_page = get_page_by_path( 'coming-soon' );

	if ( ! $coming_soon_page ) {
		global $wp_query;
		$wp_query->set_404();
		status_header( 404 );
		nocache_headers();
		include get_404_template();
		exit;
	}

	if ( FB_COMING_SOON_MODE === 'redirect' ) {
		nocache_headers();
		header( 'X-Accel-Expires: 0' );
		wp_redirect( get_permalink( $coming_soon_page->ID ), 307 );
		exit;
	}

	if ( ! defined( 'FB_ORIGINAL_POST_ID' ) ) {
		define( 'FB_ORIGINAL_POST_ID', get_the_ID() );
	}

	// Override the global post so template tags render coming-soon content.
	global $post, $wp_query;
	$post            = get_post( $coming_soon_page->ID );
	$wp_query->post  = $post;
	$wp_query->posts = [ $post ];
	$wp_query->post_count = 1;
	$wp_query->queried_object = $post;
	$wp_query->queried_object_id = $post->ID;
	$wp_query->is_singular = true;
	$wp_query->is_page = true;
	$wp_query->is_home = false;
	$wp_query->is_front_page = false;
	$wp_query->is_archive = false;
	$wp_query->is_404 = false;

	setup_postdata( $post );

	// Tell bots this is temporarily unavailable and to re-crawl in one week.
	$is_bot = isset( $_SERVER['HTTP_USER_AGENT'] )
		&& preg_match( '/bot|crawl|slurp|spider/i', $_SERVER['HTTP_USER_AGENT'] );

	if ( $is_bot ) {
		status_header( 503 );
		header( 'Retry-After: ' . ( WEEK_IN_SECONDS ) );
	}

	header( 'X-Accel-Expires: 0' );
	nocache_headers();

	// Inject noindex so search engines don't index coming-soon content at this URL.
	add_action( 'wp_head', function () {
		echo '<meta name="robots" content="noindex, nofollow">' . "\n";
	}, 1 );

	// Swap in the coming-soon page's template file.
	add_filter( 'template_include', function ( $template ) use ( $coming_soon_page ) {
		$slug = get_page_template_slug( $coming_soon_page->ID );
		if ( $slug && ( $t = locate_template( $slug ) ) ) {
			return $t;
		}
		$fallback = locate_template( [ 'page.php', 'singular.php', 'index.php' ] );
		if ( $fallback ) {
			return $fallback;
		}
		return $template;
	} );
} );
