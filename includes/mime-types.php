<?php
/**
 * Mime Types
 *
 * @package    Widget_Importer_Exporter
 * @subpackage Functions
 * @copyright  Copyright (c) 2013 - 2018, ChurchThemes.com
 * @link       https://churchthemes.com/plugins/widget-importer-exporter/
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

	//$mime_types['wie'] = 'application/json'; // 5.0.1 breaking change: https://make.wordpress.org/core/2018/12/13/backwards-compatibility-breaks-in-5-0-1/
	$mime_types['wie'] = 'text/plain';

	return $mime_types;

}

add_filter( 'upload_mimes', 'wie_add_mime_types' );


/**
 * Allow .wie as text/html for 4.9.9 / 5.0.1
 *
 * This is a workaround for a WordPress 4.9.9 / 5.0.1 update forcing upload mime type to match extension.
 * .wie is usually detected as text/plain but sometimes text/html, so text/html uploads fail.
 *
 * https://make.wordpress.org/core/2018/12/13/backwards-compatibility-breaks-in-5-0-1/
 *
 * The same occurs with .csv uploads in core being text/csv or text/plain:
 * https://core.trac.wordpress.org/ticket/45615
 *
 * This workaround is based on a CSV workaround for core by rmpel:
 * https://gist.github.com/rmpel/e1e2452ca06ab621fe061e0fde7ae150
 *
 * This is called in includes/import.php by wie_upload_import_file() so that it only happens during upload via this
 * plugin. add_filter( 'wp_check_filetype_and_ext', 'wie_allow_multiple_mime_types', 10, 4 );
 */
function wie_allow_multiple_mime_types( $values, $file, $filename, $mimes ) {

	if ( extension_loaded( 'fileinfo' ) ) {

		$finfo     = finfo_open( FILEINFO_MIME_TYPE );
		$real_mime = finfo_file( $finfo, $file );

		finfo_close( $finfo );

		if ( 'text/html' === $real_mime && preg_match( '/\.(wie)$/i', $filename ) ) {
			$values['ext']  = 'wie';
			$values['type'] = 'text/plain';
		}

	} else {

		if ( preg_match( '/\.(wie)$/i', $filename ) ) {
			$values['ext']  = 'wie';
			$values['type'] = 'text/plain';
		}

	}

	return $values;

}

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
