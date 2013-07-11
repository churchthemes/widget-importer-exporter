<?php
/**
 * Admin Page Functions
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
 * Add import/export page under Tools
 *
 * @since 0.1
 */
function wie_add_import_export_page() {

	add_management_page(
		__( 'Widget Importer & Exporter', 'widget-importer-exporter' ), // page title
		__( 'Widget Import/Export', 'widget-importer-exporter' ), // menu title
		'manage_options', // capability
		'widget-importer-exporter', // menu slug
		'wie_import_export_page_content' // callback for displaying page content
	);

}

add_action( 'admin_menu', 'wie_add_import_export_page' ); // register post type

/**
 * Import/export page content
 *
 * @since 0.1
 */
function wie_import_export_page_content() {

	?>

	<div class="wrap">

		<?php screen_icon(); ?>

		<h2><?php _e( 'Widget Importer & Exporter', 'widget-importer-exporter' ); ?></h2>

		<h3 class="title"><?php _ex( 'Import Widgets', 'heading', 'widget-importer-exporter' ); ?></h3>

		<p>
			<?php _e( 'Please select a .json file to import.', 'widget-importer-exporter' ); ?>
		</p>

		<form method="post" action="">
		
			<?php wp_nonce_field( 'wie_import_nonce', 'wie_import_nonce' ); ?>

			<input type="file" name="wie_import_file" id="wie-import-file" />

			<?php submit_button( _x( 'Import Widgets', 'button', 'widget-importer-exporter' ) ); ?>

		</form>

		<h3 class="title"><?php _ex( 'Export Widgets', 'heading', 'widget-importer-exporter' ); ?></h3>

		<p>
			<?php _e( 'Click below to generate an importable .json file.', 'widget-importer-exporter' ); ?>
		</p>

		<p class="submit">
			<input type="button" id="wie-export-button" class="button button-primary" value="<?php _ex( 'Export Widgets', 'button', 'widget-importer-exporter' ); ?>" />
		</p>

	</div>

	<?php

}