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

// ---------------------------------------------------------------------------
// Modular Includes
// ---------------------------------------------------------------------------

require_once __DIR__ . '/inc/constants.php';
require_once __DIR__ . '/inc/meta.php';
require_once __DIR__ . '/inc/preview.php';
require_once __DIR__ . '/inc/logic.php';
require_once __DIR__ . '/inc/cache.php';
require_once __DIR__ . '/inc/admin-bar.php';
require_once __DIR__ . '/inc/admin-columns.php';
require_once __DIR__ . '/inc/migration.php';
