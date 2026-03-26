<?php

/**
 * Settings Page for Flashblocks Coming Soon.
 *
 * @package flashblocks
 */

if (! defined('ABSPATH')) {
	exit;
}

/**
 * Register the settings page.
 */
add_action('admin_menu', function () {
	add_options_page(
		'Coming Soon Settings',
		'Coming Soon',
		'manage_options',
		'flashblocks-coming-soon',
		'fb_render_coming_soon_settings_page'
	);
});

/**
 * Register settings and fields.
 */
add_action('admin_init', function () {
	register_setting('fb_coming_soon_settings_group', 'fb_coming_soon_settings', [
		'type'              => 'object',
		'sanitize_callback' => 'fb_sanitize_coming_soon_settings',
		'show_in_rest'      => [
			'schema' => [
				'type'       => 'object',
				'properties' => [
					'status'  => ['type' => 'string'],
					'page_id' => ['type' => 'integer'],
					'mode'    => ['type' => 'string'],
				],
			],
		],
		'default'           => [
			'status'  => 'off',
			'page_id' => 0,
			'mode'    => 'redirect',
		],
	]);

	add_settings_section(
		'fb_coming_soon_main_section',
		'Configuration',
		null,
		'flashblocks-coming-soon'
	);

	add_settings_field(
		'status',
		'Global Status',
		'fb_render_status_field',
		'flashblocks-coming-soon',
		'fb_coming_soon_main_section'
	);

	add_settings_field(
		'page_id',
		'Coming Soon Page',
		'fb_render_page_field',
		'flashblocks-coming-soon',
		'fb_coming_soon_main_section'
	);

	add_settings_field(
		'mode',
		'Operation Mode',
		'fb_render_mode_field',
		'flashblocks-coming-soon',
		'fb_coming_soon_main_section'
	);
});

/**
 * Sanitize settings.
 */
function fb_sanitize_coming_soon_settings($input) {
	$output = [];
	$output['status']  = isset($input['status']) && in_array($input['status'], ['off', 'sitewide', 'per-page']) ? $input['status'] : 'off';
	$output['page_id'] = isset($input['page_id']) ? (int) $input['page_id'] : 0;
	$output['mode']    = isset($input['mode']) && in_array($input['mode'], ['redirect', 'replace']) ? $input['mode'] : 'redirect';
	return $output;
}

/**
 * Render Status Field.
 */
function fb_render_status_field() {
	$settings = get_option('fb_coming_soon_settings', []);
	$status   = $settings['status'] ?? 'off';
?>
	<select name="fb_coming_soon_settings[status]">
		<option value="off" <?php selected($status, 'off'); ?>>Disabled</option>
		<option value="sitewide" <?php selected($status, 'sitewide'); ?>>Site-wide (Entire site is Coming Soon)</option>
		<option value="per-page" <?php selected($status, 'per-page'); ?>>Per-page (Toggle individual pages)</option>
	</select>
	<p class="description">Select how you want to apply the Coming Soon restriction.</p>
<?php
}

/**
 * Render Page Field.
 */
function fb_render_page_field() {
	$settings = get_option('fb_coming_soon_settings', []);
	$page_id  = $settings['page_id'] ?? 0;
	wp_dropdown_pages([
		'name'             => 'fb_coming_soon_settings[page_id]',
		'selected'         => $page_id,
		'show_option_none' => 'Select a page...',
		'option_none_value' => '0',
	]);
?>
	<p class="description">This page's content will be shown to visitors when Coming Soon is active.</p>
<?php
}

/**
 * Render Mode Field.
 */
function fb_render_mode_field() {
	$settings = get_option('fb_coming_soon_settings', []);
	$mode     = $settings['mode'] ?? 'redirect';
?>
	<label>
		<input type="radio" name="fb_coming_soon_settings[mode]" value="redirect" <?php checked($mode, 'redirect'); ?>>
		<strong>Redirect (Recommended)</strong> — Temporary 307 redirect to the coming soon page. Best for caching.
	</label><br>
	<label>
		<input type="radio" name="fb_coming_soon_settings[mode]" value="replace" <?php checked($mode, 'replace'); ?>>
		<strong>Replace (Mask)</strong> — Shows coming-soon content at the original URL. Best for preserving bookmarks.
	</label>
<?php
}

/**
 * Render the settings page.
 */
function fb_render_coming_soon_settings_page() {
?>
	<div class="wrap">
		<h1>Flashblocks Coming Soon</h1>
		<form action="options.php" method="post">
			<?php
			settings_fields('fb_coming_soon_settings_group');
			do_settings_sections('flashblocks-coming-soon');
			submit_button();
			?>
		</form>

		<hr>

		<h2>Instructions & Usage</h2>
		<div class="card">
			<h3>How to use</h3>
			<ol>
				<li><strong>Site-wide mode</strong>: Every page on the site will show the coming-soon content.</li>
				<li><strong>Per-page mode</strong>: Go to any Page or Post and use the "Coming Soon" toggle in the sidebar.</li>
			</ol>

			<h3>Previewing</h3>
			<p>If you need to share a page with someone who isn't logged in, give them a link with one of these query strings at the end:
				<br><code>?preview</code> - this will allow them to preview that single page.
				<br><code>?preview-on</code> - this will set a cookie that bypasses all coming soon pages for 1 month.
				<br><code>?preview-off</code> - this will clear the preview cookie immediately.
			</p>
		</div>
	</div>
	<style>
		.card {
			max-width: 800px;
			padding: 1em 2em;
			margin-top: 2em;
		}
	</style>
<?php
}
