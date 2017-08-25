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

update_option( 'wie_php_notice_reminder', '' );
update_option( 'wie_php_notice_dismissed', '' );
update_option( 'wie_http_notice_reminder', '' );
update_option( 'wie_http_notice_dismissed', '' );

/**
 * Activate security notices
 *
 * @since 1.5
 * @return array Notices.
 */
function wie_security_notices() {

	$notices = array();

	// Outdated PHP notice.
	$notices[] = 'wie_php_notice';

	// HTTP notice.
	$notices[] = 'wie_http_notice';

	// Filter notices.
	$notices = apply_filters( 'wie_security_notices', $notices );

	// Loop notices.
	foreach ( $notices as $notice ) {
		add_action( 'admin_notices', $notice );
	}

}

add_action( 'init', 'wie_security_notices' );

/**
 * Show security notice?
 *
 * The notice should only be shown if certain conditions are met.
 *
 * @since 1.5
 * @param string $type php or http.
 * @return bool True if notice should be shown.
 */
function wie_show_security_notice( $type ) {

	// Show unless there is reason not to.
	$show = true;

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

	// Type of notice.
	if ( 'php' === $type ) {

		// PHP version.
		$php_version_used = phpversion();
		$php_version_required = '5.7'; // notice shows if lower than this version.

		// Only if PHP version is outdated.
		if ( version_compare( $php_version_used, $php_version_required, '>=' ) ) {
			$show = false;
		}

		// Set option prefix.
		$option_prefix = 'wie_php_notice';

	} elseif ( 'http' === $type ) {

		// Only if HTTPS is not used.
		// is_ssl() no reliable with load balancers so instead check if Settings > General is using an https URL.
		if ( preg_match( '/^https:.*/', get_bloginfo( 'url' ) ) ) {
			$show = false;
		}

		// Set option prefix.
		$option_prefix = 'wie_http_notice';

	} else { // invalid type.
		$show = false;
	}

	// Only if not already dismissed.
	if ( get_option( $option_prefix . '_dismissed' ) ) {
		$show = false;
	}

	// Only if X days has not passed since time "Remind Later" was clicked
	$reminder_time = get_option( $option_prefix . '_reminder' ); // timestamp for moment "Remind Later" was set.
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
 * @since 1.5
 */
function wie_php_notice() {

	// Only on WIE and Dashboard screens when user is Administrator and notice has not been dismissed.
	if ( ! wie_show_security_notice( 'php' ) ) {
		return;
	}

	// URL with instructions for fixing.
	$fix_url = 'https://wpultimate.com/update-php-wordpress';

	// Output notice.
	?>

	<div id="ctc-outdated-php-notice" class="notice notice-warning is-dismissible">

		<p>

			<?php

			printf(
				wp_kses(
					/* translators: %1$s is PHP version used, %2$s is URL to guide with instructions for fixing */
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

/**
 * Show notice when https not used
 *
 * @since 1.5
 */
function wie_http_notice() {

	// Only on WIE and Dashboard screens when user is Administrator and notice has not been dismissed.
	if ( ! wie_show_security_notice( 'http' ) ) {
		return;
	}

	// URL with instructions for fixing.
	$fix_url = 'https://wpultimate.com/ssl-https-wordpress';

	// Output notice.
	?>

	<div id="ctc-outdated-http-notice" class="notice notice-warning is-dismissible">

		<p>

			<?php

			printf(
				wp_kses(
					/* translators: %1$s is URL to guide with instructions for fixing */
					__( '<b>HTTP Security Warning:</b> Your website is not using HTTPS/SSL. This creates a security risk. <b><a href="%1$s" target="_blank">Fix This Now</a></b> <a href="#" id="wie-notice-remind">Remind Later</a>', 'widget-importer-exporter' ),
					array(
						'b' => array(),
						'a' => array(
							'href' => array(),
							'target' => array(),
							'id' => array(),
						),
					)
				),
				esc_url( $fix_url )
			);

			?>

		</p>

	</div>

	<?php

}

/**
 * JavaScript for remembering notice was dismissed
 *
 * Since normally the dismiss button only closes notice for current page view.
 * this uses AJAX to set an option so that the notice can be hidden indefinitely.
 *
 * @since 1.5
 */
function wie_php_dismiss_notice_js() {

	// Only on WIE and Dashboard screens when user is Administrator and notice has not been dismissed.
	if ( ! wie_show_security_notice( 'php' ) ) {
		return;
	}

	// Nonce.
	$ajax_nonce = wp_create_nonce( 'wie_php_dismiss_notice' );

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
					action: 'wie_php_dismiss_notice',
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
					action: 'wie_php_dismiss_notice',
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

add_action( 'admin_print_footer_scripts', 'wie_php_dismiss_notice_js' );

/**
 * Set option to prevent notice from showing again
 *
 * This is called by AJAX in wie_php_dismiss_notice_js()
 *
 * @since 1.5
 */
function wie_php_dismiss_notice() {

	// Only if is AJAX request.
	if ( ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
		return;
	}

	// Check nonce.
	check_ajax_referer( 'wie_php_dismiss_notice', 'security' );

	// Only if user is Administrator.
	if ( ! current_user_can( 'administrator' ) ) {
		return;
	}

	// Update option so notice is not shown again.
	if ( ! empty( $_REQUEST['reminder'] ) ) {
		update_option( 'wie_php_notice_reminder', current_time( 'timestamp' ) );
	} else {
		update_option( 'wie_php_notice_dismissed', '1' );
	}

}

add_action( 'wp_ajax_wie_php_dismiss_notice', 'wie_php_dismiss_notice' );
