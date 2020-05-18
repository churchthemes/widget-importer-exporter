<?php
/**
 * Export Functions
 *
 * @package    Widget_Importer_Exporter
 * @subpackage Functions
 * @copyright  Copyright (c) 2013 - 2020, ChurchThemes.com, LLC
 * @link       https://churchthemes.com/plugins/widget-importer-exporter/
 * @license    https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0-or-later
 * @since      0.1
 */

defined('ABSPATH') || exit; // No direct access.

/**
 * Generate export data
 *
 * @since 0.1
 * @return string Export file contents
 */
function wie_generate_export_data()
{
	// Get all available widgets site supports.
	$available_widgets = wie_available_widgets();

	// Get all widget instances for each widget.
	$widget_instances = array();

	// Loop widgets.
	foreach ($available_widgets as $widget_data) {
		// Get all instances for this ID base.
		$instances = get_option('widget_' . $widget_data['id_base']);

		// Have instances.
		if (! empty($instances)) {
			// Loop instances.
			foreach ($instances as $instance_id => $instance_data) {
				// Key is ID (not _multiwidget).
				if (is_numeric($instance_id)) {
					$unique_instance_id                    = $widget_data['id_base'] . '-' . $instance_id;
					$widget_instances[$unique_instance_id] = $instance_data;
				}
			}
		}
	}

	// Gather sidebars with their widget instances.
	$sidebars_widgets          = get_option('sidebars_widgets');
	$sidebars_widget_instances = array();
	foreach ($sidebars_widgets as $sidebar_id => $widget_ids) {
		// Skip inactive widgets.
		if ('wp_inactive_widgets' === $sidebar_id) {
			continue;
		}

		// Skip if no data or not an array (array_version).
		if (! is_array($widget_ids) || empty($widget_ids)) {
			continue;
		}

		// Loop widget IDs for this sidebar.
		foreach ($widget_ids as $widget_id) {
			// Is there an instance for this widget ID?
			if (isset($widget_instances[$widget_id])) {
				// Add to array.
				$sidebars_widget_instances[$sidebar_id][$widget_id] = $widget_instances[$widget_id];
			}
		}
	}

	// Filter pre-encoded data.
	$data = apply_filters('wie_unencoded_export_data', $sidebars_widget_instances);

	// Encode the data for file contents.
	$encoded_data = wp_json_encode($data);

	// Return contents.
	return apply_filters('wie_generate_export_data', $encoded_data);
}

/**
 * Send export file to user
 *
 * Triggered by URL like /wp-admin/tools.php?page=widget-importer-exporter&export=1
 *
 * The data is JSON with .wie extension in order not to confuse export files with other plugins.
 *
 * @since 0.1
 */
function wie_send_export_file()
{
	// Export requested.
	if (! empty($_GET['export'])) {
		// Check referer before doing anything else.
		check_admin_referer('wie_export', 'wie_export_nonce');

		// Build filename
		// Single Site: yoursite.com-widgets.wie
		// Multisite: site.multisite.com-widgets.wie or multisite.com-site-widgets.wie.
		$site_url  = site_url('', 'http');
		$site_url  = trim($site_url, '/\\'); // Remove trailing slash.
		$filename  = str_replace('http://', '', $site_url); // Remove http://.
		$filename  = str_replace(array('/', '\\'), '-', $filename); // Replace slashes with - .
		$filename .= '-widgets.wie'; // Append.
		$filename  = apply_filters('wie_export_filename', $filename);

		// Generate export file contents.
		$file_contents = wie_generate_export_data();
		$filesize      = strlen($file_contents);

		// Headers to prompt "Save As".
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename=' . $filename);
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . $filesize);

		// Clear buffering just in case.
		// @codingStandardsIgnoreLine
		@ob_end_clean();
		flush();

		// Output file contents.
		// @codingStandardsIgnoreLine
		echo $file_contents;

		// Stop execution.
		exit;
	}
}

add_action('load-tools_page_widget-importer-exporter', 'wie_send_export_file');
