<?php
/**
 * Export Functions
 *
 * @package    Widget_Importer_Exporter
 * @subpackage Functions
 * @copyright  Copyright (c) 2013, DreamDolphin Media, LLC
 * @link       https://github.com/stevengliebe/widget-importer-exporter
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @since      0.1
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Generate export data
 *
 * @since 0.1
 * @global array $wp_registered_widget_controls
 * @return string Export file contents
 */
function wie_generate_export_data() {

	global $wp_registered_widget_controls;

	// Get all widget ID bases
	// Surely there is a better way to get this data?
	$widget_controls = $wp_registered_widget_controls;
	$widget_id_bases = array();
	foreach ( $widget_controls as $widget ) {

		// Gather unique ID bases into array
		if ( ! empty( $widget['id_base'] ) && ! in_array( $widget['id_base'], $widget_id_bases ) ) { // no dupes
			$widget_id_bases[] = $widget['id_base'];
		}

	}

	// Get all widget instances for each ID base
	$widget_instances = array();
	foreach ( $widget_id_bases as $id_base ) {

		// Get all instances for this ID base
		$instances = get_option( 'widget_' . $id_base );

		// Have instances
		if ( ! empty( $instances ) ) {

			// Loop instances
			foreach ( $instances as $instance_id => $instance_data ) {

				// Key is ID (not _multiwidget)
				if ( is_numeric( $instance_id ) ) {
					$unique_instance_id = $id_base . '-' . $instance_id;
					$widget_instances[$unique_instance_id] = $instance_data;
				}

			}

		}

	}

	// Gather sidebars with their widget instances
	$sidebars_widgets = get_option( 'sidebars_widgets' ); // get sidebars and their unique widgets IDs
	$sidebars_widget_instances = array();
	foreach ( $sidebars_widgets as $sidebar_id => $widget_ids ) {

		// Skip inactive widgets
		if ( 'wp_inactive_widgets' == $sidebar_id ) {
			continue;
		}

		// Skip if no data or not an array (array_version)
		if ( ! is_array( $widget_ids ) || empty( $widget_ids ) ) {
			continue;
		}

		// Loop widget IDs for this sidebar
		foreach ( $widget_ids as $widget_id ) {

			// Is there an instance for this widget ID?
			if ( isset( $widget_instances[$widget_id] ) ) {

				// Add to array
				$sidebars_widget_instances[$sidebar_id][$widget_id] = $widget_instances[$widget_id];

			}

		}

	}

	// Filter pre-encoded data
	$data = apply_filters( 'wie_unencoded_export_data', $sidebars_widget_instances );

	// Encode the data for file contents
	$encoded_data = json_encode( $data );

	// Return contents
	return apply_filters( 'wie_generate_export_data', $encoded_data );

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
function wie_send_export_file() {

	// Export requested
	if ( ! empty( $_GET['export'] ) ) {

		// Build filename
		// Single Site: yoursite.com-widgets.wie
		// Multisite: site.multisite.com-widgets.wie or multisite.com-site-widgets.wie
		$site_url = site_url( '', 'http' );
		$site_url = trim( $site_url, '/\\' ); // remove trailing slash
		$filename = str_replace( 'http://', '', $site_url ); // remove http://
		$filename = str_replace( array( '/', '\\' ), '-', $filename ); // replace slashes with -
		$filename .= '-widgets.wie'; // append
		$filename = apply_filters( 'wie_export_filename', $filename );

		// Generate export file contents
		$file_contents = wie_generate_export_data();
		$filesize = strlen( $file_contents );

		// Headers to prompt "Save As"
		header( 'Content-Type: application/octet-stream' );
		header( 'Content-Disposition: attachment; filename=' . $filename );
		header( 'Expires: 0' );
		header( 'Cache-Control: must-revalidate' );
		header( 'Pragma: public' );
		header( 'Content-Length: ' . $filesize );

		// Clear buffering just in case
		@ob_end_clean();
		flush();

		// Output file contents
		echo $file_contents;

		// Stop execution
		exit;

	}

}

add_action( 'load-tools_page_widget-importer-exporter', 'wie_send_export_file' );
