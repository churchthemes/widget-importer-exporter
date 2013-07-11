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
 * Generate export file contents
 *
 * @since 0.1
 * @return string Export file contents
 */
function wie_generate_export_contents() {

	// Make file contents
	$contents = 'whatever';

	// Return contents
	return apply_filters( 'wie_generate_export_contents', $contents );

}

/**
 * Send export file to user
 *
 * Triggered by URL like /wp-admin/tools.php?page=widget-importer-exporter&export=1
 *
 * @since 0.1
 */
function wie_send_export_file() {

	// Export requested
	if ( ! empty( $_GET['export'] ) ) {

		// Build filename
		// Single Site: yoursite.com-widgets.json
		// Multisite: site.multisite.com-widgets.json or multisite.com-site-widgets.json
		$site_url = site_url( '', 'http' );
		$site_url = trim( $site_url, '/\\' ); // remove trailing slash
		$filename = str_replace( 'http://', '', $site_url ); // remove http://
		$filename = str_replace( array( '/', '\\' ), '-', $filename ); // replace slashes with -
		$filename .= '-widgets.json'; // append
		$filename = apply_filters( 'wie_export_filename', $filename );

		// Generate export file contents
		$file_contents = wie_generate_export_contents();
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