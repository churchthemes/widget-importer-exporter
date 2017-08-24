<?php
/**
 * Mime Types
 *
 * @package    Widget_Importer_Exporter
 * @subpackage Functions
 * @copyright  Copyright (c) 2013 - 2017, WP Ultimate
 * @link       https://wpultimate.com/widget-importer-exporter
 * @license    GPLv2 or later
 * @since      0.1
 */

// No direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add mime type for upload
 *
 * Make sure the WordPress install will accept .wie uploads.
 *
 * @since 0.1
 * @param array $mime_types Currently uploadable mime types.
 * @return array Mime types with additions
 */
function wie_add_mime_types( $mime_types ) {

	$mime_types['wie'] = 'application/json';

	return $mime_types;

}

add_filter( 'upload_mimes', 'wie_add_mime_types' );

/**
 * Disable real MIME check on WordPress 4.7.1 and 4.7.2
 *
 * This is a workaround for a WordPress 4.7.1 bug affecting uploads. Other versions not affected.
 * This workaround will only take effect on installs of 4.7.1 and only during import.
 *
 * This is called in includes/import.php by wie_upload_import_file() so that it only happens during upload via this
 * plugin. add_filter( 'wp_check_filetype_and_ext', 'wie_disable_real_mime_check', 10, 4 );
 *
 * Based on the Disable Real MIME Check plugin by Sergey Biryukov:
 * https://wordpress.org/plugins/disable-real-mime-check/ More information:
 * https://wordpress.org/support/topic/solution-for-wp-4-7-1-bug-causing-you-must-upload-a-wie-file-generated-by/
 */
function wie_disable_real_mime_check( $data, $file, $filename, $mimes ) {

	$wp_version = get_bloginfo( 'version' );

	// WordPress 4.7.1 - 4.7.3 are affected only.
	// 4.7.2 and 4.7.3 were rushed out as security updates without the upload bug being fixed.
	if ( ! in_array( $wp_version, array( '4.7.1', '4.7.2', '4.7.3' ), true ) ) {
		return $data;
	}

	$wp_filetype = wp_check_filetype( $filename, $mimes );

	$ext             = $wp_filetype['ext'];
	$type            = $wp_filetype['type'];
	$proper_filename = $data['proper_filename'];

	return compact( 'ext', 'type', 'proper_filename' );

}
