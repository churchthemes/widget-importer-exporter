<?php
/**
 * Admin Functions
 *
 * General admin area functions. Also see page.php.
 *
 * @package    Widget_Importer_Exporter
 * @subpackage Functions
 * @copyright  Copyright (c) 2017, churchthemes.com
 * @link       https://churchthemes.com/plugins/widget-importer-exporter
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @since      1.4
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Add Plugin Action Link
 *
 * This will insert an "Import/Export" link into the plugin's action links (Plugin page's list)
 *
 * @since 1.4
 * @param array $links Existing action links
 * @return array Modified action links
 */
function wie_add_plugin_action_link( $links ) {

	// Have links array?
	if ( is_array( $links ) ) {

		// Append "Settings" link
		$links[] = '<a href="' . admin_url( 'tools.php?page=widget-importer-exporter' ) . '">' . __( 'Import/Export', 'widget-importer-exporter' ) . '</a>';

	}

	return $links;

}

add_filter( 'plugin_action_links_' . plugin_basename( WIE_FILE ), 'wie_add_plugin_action_link' );