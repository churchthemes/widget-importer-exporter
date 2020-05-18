<?php
/**
 * Admin Functions
 *
 * General admin area functions. Also see page.php.
 *
 * @package    Widget_Importer_Exporter
 * @subpackage Functions
 * @copyright  Copyright (c) 2017 - 2020, ChurchThemes.com, LLC
 * @link       https://churchthemes.com/plugins/widget-importer-exporter/
 * @license    https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0-or-later
 * @since      1.4
 */

defined('ABSPATH') || exit; // No direct access.

/**
 * Enqueue admin styles.
 *
 * @since 1.5
 */
function wie_enqueue_styles() {
	// Get current screen.
	$screen = get_current_screen();

	// Only on WIE and Dashboard screens.
	if (! in_array($screen->base, array('dashboard', 'tools_page_widget-importer-exporter'), true)) {
		return;
	}

	// Enqueue styles
	wp_enqueue_style('wie-main', WIE_URL . '/' . WIE_CSS_DIR . '/style.css', false, WIE_VERSION); // Bust cache on update.
}

add_action('admin_enqueue_scripts', 'wie_enqueue_styles'); // admin-end only.

/**
 * Enqueue admin scripts.
 *
 * @since 1.6
 */
function wie_enqueue_scripts() {
	// Get current screen.
	$screen = get_current_screen();

	// Only on WIE screen.
	if (! in_array($screen->base, array('dashboard', 'tools_page_widget-importer-exporter'), true)) {
		return;
	}

	// Enqueue script
	wp_enqueue_script('wie-main', WIE_URL . '/' . WIE_JS_DIR . '/main.js', array('jquery'), WIE_VERSION, false); // bust cache on update.
}

add_action('admin_enqueue_scripts', 'wie_enqueue_scripts'); // admin-end only.

/**
 * Add plugin action link.
 *
 * Insert an "Import/Export" link into the plugin's action links (Plugin page's list)
 *
 * @since 1.4
 * @param array $links Existing action links.
 * @return array Modified action links
 */
function wie_add_plugin_action_link($links)
{
	// If has permission.
	if (! current_user_can('edit_theme_options')) {
		return array();
	}

	// Have links array?
	if (is_array($links)) {
		// Append "Settings" link.
		$links[] = sprintf(
			'<a href="%1$s">%2$s</a>',
			esc_url(admin_url('tools.php?page=widget-importer-exporter')),
			esc_html__('Import/Export', 'widget-importer-exporter')
		);
	}

	return $links;
}

add_filter('plugin_action_links_' . plugin_basename(WIE_FILE), 'wie_add_plugin_action_link');

/**
 * Add link on Widgets page
 *
 * Insert an "Import/Export" link on the Widgets screen after 'Manage with Live Preview'.
 * This is done with JavaScript since there is no hook for this area.
 *
 * @since 1.4
 */
function wie_add_widgets_screen_link()
{
	// Build link with same style as 'Manage with Live Preview'.
	$link_html = sprintf(
		wp_kses(
			' <a href="%1$s" class="page-title-action">%2$s</a>',
			array(
				// Link tag only.
				'a' => array(
					'href'  => array(),
					'class' => array(),
				),
			)
		),
		esc_url(admin_url('tools.php?page=widget-importer-exporter')),
		esc_html__('Import/Export', 'widget-importer-exporter')
	);

	// Output JavaScript to insert link after 'Manage with Live Preview'.
	?>

	<script type="text/javascript">
		jQuery(document).ready(function ($) {
			// Encode string for security
			var link_html = <?php echo wp_json_encode($link_html); ?>;

			// Insert after last button by title
			$('.page-title-action').last().after(link_html);
		});
	</script>
	<?php
}

// WP 4.6+.
add_action('admin_print_footer_scripts-widgets.php', 'wie_add_widgets_screen_link');
