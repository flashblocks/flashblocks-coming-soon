<?php

/**
 * Plugin Name:  Flashblocks Coming Soon
 * Description:  Per-page or site-wide coming soon toggle via post meta. Shows coming-soon content at the real URL or redirects, depending on the configured mode.
 * Version:      1.0.0
 * Requires at least: 6.7
 * Requires PHP: 7.4
 * Author:       Sunny Morgan
 * License:      GPL-2.0-or-later
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:  flashblocks-coming-soon
 * Flashblocks Module: yes
 * Flashblocks Category: utility
 * Flashblocks Tags: coming-soon, maintenance
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


// ---------------------------------------------------------------------------
// Modular Includes
// ---------------------------------------------------------------------------

require_once __DIR__ . '/inc/settings.php';
require_once __DIR__ . '/inc/meta.php';
require_once __DIR__ . '/inc/preview.php';
require_once __DIR__ . '/inc/logic.php';
require_once __DIR__ . '/inc/cache.php';
require_once __DIR__ . '/inc/admin-bar.php';
require_once __DIR__ . '/inc/admin-columns.php';
require_once __DIR__ . '/inc/bulk-edit.php';
