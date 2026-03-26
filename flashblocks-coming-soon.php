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

if (! defined('ABSPATH')) {
	exit;
}

/**
 * Constants and configuration for Flashblocks Coming Soon.
 *
 * @package flashblocks
 */
define('FB_COMING_SOON_META', '_fb_coming_soon');
define('FB_COMING_SOON_MODE', 'replace'); // 'redirect' or 'replace'


// ---------------------------------------------------------------------------
// Modular Includes
// ---------------------------------------------------------------------------

require_once __DIR__ . '/inc/meta.php';
require_once __DIR__ . '/inc/preview.php';
require_once __DIR__ . '/inc/logic.php';
require_once __DIR__ . '/inc/cache.php';
require_once __DIR__ . '/inc/admin-bar.php';
require_once __DIR__ . '/inc/admin-columns.php';
