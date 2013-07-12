<?php
/**
 * Import Functions
 *
 * @package    Widget_Importer_Exporter
 * @subpackage Functions
 * @copyright  Copyright (c) 2013, DreamDolphin Media, LLC
 * @link       https://github.com/stevengliebe/widget-importer-exporter
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @since      0.3
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Upload widget file
 *
 * @since 0.3
 */
function wie_upload_import_file() {

	// Check nonce for security since there's a post
	if ( ! empty( $_POST ) && check_admin_referer( 'wie_import', 'wie_import_nonce' ) ) { // check_admin_referer prints fail page and dies

print_r($_FILES);
exit;

	}

}

add_action( 'load-tools_page_widget-importer-exporter', 'wie_upload_import_file' );
