<?php
/**
 * Widget Functions
 *
 * @package    Widget_Importer_Exporter
 * @subpackage Functions
 * @copyright  Copyright (c) 2013, DreamDolphin Media, LLC
 * @link       https://github.com/stevengliebe/widget-importer-exporter
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @since      0.4
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Available widgets
 *
 * Gather site's widgets into array with ID base, name, etc.
 * Used by export and import functions.
 *
 * @since 0.4
 * @global array $wp_registered_widget_updates
 * @return array Widget information
 */
function wie_available_widgets() {

	global $wp_registered_widget_controls;

	$widget_controls = $wp_registered_widget_controls;

	$available_widgets = array();

	foreach ( $widget_controls as $widget ) {

		if ( ! empty( $widget['id_base'] ) && ! isset( $available_widgets[$widget['id_base']] ) ) { // no dupes

			$available_widgets[$widget['id_base']]['id_base'] = $widget['id_base'];
			$available_widgets[$widget['id_base']]['name'] = $widget['name'];

		}

	}

	return apply_filters( 'wie_available_widgets', $available_widgets );

}