<?php
/**
 * Notice Functions
 *
 * Admin notice functions.
 *
 * @package    Widget_Importer_Exporter
 * @subpackage Functions
 * @copyright  Copyright (c) 2017, WP Ultimate
 * @link       https://wpultimate.com/widget-importer-exporter
 * @license    GPLv2 or later
 * @since      1.5
 */

// No direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Show outdated PHP notice
 *
 * The notice should only be shown if certain conditions are met.
 *
 * @since 1.5
 * @return bool True if notice should be shown
 */
function wie_outdated_php_show_notice() {

	// Show unless there is reason not to.
	$show = true;

	// PHP version.
	$php_version_used = phpversion();
	$php_version_required = '5.7'; // notice shows if lower than this version.

	// Prepare for "Remind Later" link.
	$current_time = current_time( 'timestamp' );
	$reminder_days = 7; // show notice X days after "Remind Later" is clicked.

	// Get current screen.
	$screen = get_current_screen();

	// Only on WIE and Dashboard screens.
	if ( ! in_array( $screen->base, array( 'dashboard', 'tools_page_widget-importer-exporter' ), true ) ) {
		$show = false;
	}

	// Only if user is Administrator.
	if ( ! current_user_can( 'administrator' ) ) {
		$show = false;
	}

	// Only if PHP version is outdated.
	if ( version_compare( $php_version_used, $php_version_required, '>=' ) ) {
		$show = false;
	}

	// Only if not already dismissed.
	if ( get_option( 'wie_outdated_php_notice_dismissed' ) ) {
		$show = false;
	}

	// Only if X days has not passed since time "Remind Later" was clicked
	$reminder_time = get_option( 'wie_outdated_php_notice_reminder' ); // timestamp for moment "Remind Later" was set.
	if ( $reminder_time ) { // Only check if a reminder was set.

		$reminder_seconds = $reminder_days * DAY_IN_SECONDS; // Seconds to wait until notice shows again.
		$reminder_time_end = $reminder_time + $reminder_seconds; // Timestamp that must be in past for notice to show again.

		if ( $reminder_time && $current_time < $reminder_time_end ) {
			$show = false;
		}

	}

	return $show;

}

/**
 * Show notice if PHP is outdated
 *
 * This will show only on WIE and Dashboard screens if user is an Administrator and notice has not been dismissed.
 *
 * @since 1.5
 */
function wie_outdated_php_notice() {

	// Only on WIE and Dashboard screens when user is Administrator and notice has not been dismissed.
	if ( ! wie_outdated_php_show_notice() ) {
		return;
	}

	// URL with instructions for fixing.
	$fix_url = 'https://wpultimate.com/update-php-wordpress'

	// Output notice.
	?>

	<div id="ctc-outdated-php-notice" class="notice notice-warning is-dismissible">

		<p>

			<?php

			printf(
				wp_kses(
					/* translators: %1$s is URL to guide with instructions for fixing, %2$s is PHP version used */
					__( '<b>PHP Security Warning:</b> Your version of PHP is <b>%1$s</b> which is outdated and insecure. <b><a href="%2$s" target="_blank">Fix This Now</a></b> <a href="#" id="wie-notice-remind">Remind Later</a>', 'widget-importer-exporter' ),
					array(
						'b' => array(),
						'a' => array(
							'href' => array(),
							'target' => array(),
							'id' => array(),
						),
					)
				),
				esc_html( phpversion() ),
				esc_url( $fix_url )
			);

			?>

		</p>

	</div>

	<?php

}

add_action( 'admin_notices', 'wie_outdated_php_notice' );

/**
 * JavaScript for remembering outdated PHP notice was dismissed
 *
 * Since normally the dismiss button only closes notice for current page view.
 * this uses AJAX to set an option so that the notice can be hidden indefinitely.
 *
 * @since 1.5
 */
function wie_outdated_php_dismiss_notice_js() {

	// Only on WIE and Dashboard screens when user is Administrator and notice has not been dismissed.
	if ( ! wie_outdated_php_show_notice() ) {
		return;
	}

	// Nonce.
	$ajax_nonce = wp_create_nonce( 'wie_outdated_php_dismiss_notice' );

	// JavaScript for detecting click on dismiss icon.
	?>

	<script type="text/javascript">

	jQuery( document ).ready( function( $ ) {

		// Dismiss icon
		$( document ).on( 'click', '#ctc-outdated-php-notice .notice-dismiss', function() {

   			// Send request.
			$.ajax( {
				url: ajaxurl,
				data: {
					action: 'wie_outdated_php_dismiss_notice',
					security: '<?php echo esc_js( $ajax_nonce ); ?>',
				},
			} );

		} );

		// Remind later link
		$( document ).on( 'click', '#wie-notice-remind', function() {

			// Stop click to URL.
			event.preventDefault();

   			// Send request.
			$.ajax( {
				url: ajaxurl,
				data: {
					action: 'wie_outdated_php_dismiss_notice',
					security: '<?php echo esc_js( $ajax_nonce ); ?>',
					reminder: true,
				},
			} );

			// Fade out notice.
			$( '#ctc-outdated-php-notice' ).fadeOut( 'fast' );

		} );

	} );

	</script>

	<?php

}

add_action( 'admin_print_footer_scripts', 'wie_outdated_php_dismiss_notice_js' );

/**
 * Set option to prevent notice from showing again
 *
 * This is called by AJAX in wie_outdated_php_dismiss_notice_js()
 *
 * @since 1.5
 */
function wie_outdated_php_dismiss_notice() {

	// Only if is AJAX request.
	if ( ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
		return;
	}

	// Check nonce.
	check_ajax_referer( 'wie_outdated_php_dismiss_notice', 'security' );

	// Only if user is Administrator.
	if ( ! current_user_can( 'administrator' ) ) {
		return;
	}

	// Update option so notice is not shown again.
	if ( ! empty( $_REQUEST['reminder'] ) ) {
		update_option( 'wie_outdated_php_notice_reminder', current_time( 'timestamp' ) );
	} else {
		update_option( 'wie_outdated_php_notice_dismissed', '1' );
	}

}

add_action( 'wp_ajax_wie_outdated_php_dismiss_notice', 'wie_outdated_php_dismiss_notice' );
