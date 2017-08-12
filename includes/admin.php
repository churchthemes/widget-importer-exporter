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
 * Add plugin action link
 *
 * Insert an "Import/Export" link into the plugin's action links (Plugin page's list)
 *
 * @since 1.4
 * @param array $links Existing action links
 * @return array Modified action links
 */
function wie_add_plugin_action_link( $links ) {

	// If has permission
	if ( ! current_user_can( 'edit_theme_options' ) ) { // can manage Appearance > Widgets
		return false;
	}

	// Have links array?
	if ( is_array( $links ) ) {

		// Append "Settings" link
		$links[] = '<a href="' . esc_url( admin_url( 'tools.php?page=widget-importer-exporter' ) ) . '">' . esc_html( __( 'Import/Export', 'widget-importer-exporter' ) ) . '</a>';

	}

	return $links;

}

add_filter( 'plugin_action_links_' . plugin_basename( WIE_FILE ), 'wie_add_plugin_action_link' );

/**
 * Add link on Widgets page
 *
 * Insert an "Import/Export" link on the Widgets screen after 'Manage with Live Preview'.
 * This is done with JavaScript since there is no hook for this area.
 *
 * @since 1.4
 */
function wie_add_widgets_screen_link() {

	// Build link with same style as 'Manage with Live Preview'
	$link_html = sprintf(
		wp_kses(
			' <a href="%1$s" class="page-title-action">%2$s</a>',
			array(
				'a' => array(  // link tag only
					'href' => array(),
					'class' => array()
				)
			)
		),
		esc_url( admin_url( 'tools.php?page=widget-importer-exporter' ) ),
		esc_html( __( 'Import/Export', 'widget-importer-exporter' ) )
	);

	// Output JavaScript to insert link after 'Manage with Live Preview'
	?>

	<script type="text/javascript">

	jQuery( document ).ready( function( $ ) {

		// Encode string for security
		var link_html = <?php echo wp_json_encode( $link_html ); ?>;

		// Insert after last button by title
		$( '.page-title-action' ).last().after( link_html );

	} );

	</script>

	<?php

}

add_action( 'admin_print_footer_scripts-widgets.php', 'wie_add_widgets_screen_link' ); // WP 4.6+
