<?php
/**
 * Mime Types
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
 * Add mime type for upload
 *
 * Make sure the WordPress install will accept .wie uploads.
 *
 * @since 0.1
 * @param array $mime_types Currently uploadable mime types
 * @return array Mime types with additions
 */
function wie_add_mime_types( $mime_types ) {

	$mime_types['wie'] = 'application/json';

	return $mime_types;

}

add_filter( 'upload_mimes', 'wie_add_mime_types' );
