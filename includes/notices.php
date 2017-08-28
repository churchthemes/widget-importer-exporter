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
 * Activate security notices.
 *
 * To Do: Make this into a class that other plugins can use similarly.
 *
 * @since 1.5
 */
function wie_security_notices() {

	$notices = array();

	// Outdated PHP notice.
	$notices[] = 'wie_php_notice';

	// HTTP notice.
	$notices[] = 'wie_http_notice';

	// Filter notices.
	$notices = apply_filters( 'wie_security_notices', $notices );

	// Loop notices to activate.
	foreach ( $notices as $notice ) {
		add_action( 'admin_notices', $notice );
	}

}

add_action( 'admin_init', 'wie_security_notices' );

/**
 * Show security notice?
 *
 * Return true or false for a notice type if certain conditions are met.
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
	$option_prefix = '';
	if ( 'php' === $type ) {

		// PHP version.
		$php_version_used = phpversion();
		$php_version_required = '5.6'; // notice shows if lower than this version.

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
	if ( $option_prefix && get_option( $option_prefix . '_dismissed' ) ) {
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
 * PHP outdated notice
 *
 * @since 1.5
 */
function wie_php_notice() {

	// Only on certain conditions.
	if ( ! wie_show_security_notice( 'php' ) ) {
		return;
	}

	// Output notice.
	?>

	<div id="wie-security-notice" class="notice notice-warning is-dismissible" data-type="php">

		<p>

			<span id="wie-notice-message">

				<?php
				printf(
					wp_kses(
						/* translators: %1$s is PHP version used, %2$s is URL to guide with instructions for fixing */
						__( '<b>PHP Security Warning:</b> Your version of PHP is %1$s which is outdated and insecure. <b><a href="%2$s" target="_blank">Fix This Now</a></b>', 'widget-importer-exporter' ),
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
					'https://wpultimate.com/update-php-wordpress'
				);
				?>

			</span>

			<span id="wie-notice-remind">
				<a href="#" id="wie-notice-remind-link">
					<?php esc_html_e( 'Remind Later', 'widget-importer-exporter' ); ?>
				</a>
			</span>

		</p>

	</div>

	<?php

}

/**
 * HTTP notice
 *
 * @since 1.5
 */
function wie_http_notice() {

	// Only if showing a notice.
	if ( ! wie_show_security_notice( 'http' ) ) {
		return;
	}

	// Output notice.
	?>

	<div id="wie-security-notice" class="notice notice-warning is-dismissible" data-type="http">

		<p>

			<span id="wie-notice-message">

				<?php
				printf(
					wp_kses(
						/* translators: %1$s is URL to guide with instructions for fixing */
						__( '<b>HTTP Security Warning:</b> Your website is not using HTTPS/SSL. This is a security risk. <b><a href="%1$s" target="_blank">Fix This Now</a></b>', 'widget-importer-exporter' ),
						array(
							'b' => array(),
							'a' => array(
								'href' => array(),
								'target' => array(),
								'id' => array(),
							),
						)
					),
					'https://wpultimate.com/ssl-https-wordpress'
				);
				?>

			</span>

			<span id="wie-notice-remind">
				<a href="#" id="wie-notice-remind-link">
					<?php esc_html_e( 'Remind Later', 'widget-importer-exporter' ); ?>
				</a>
			</span>

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
function wie_dismiss_notice_js() {

	// Only when a notice is being shown.
	if ( ! wie_show_security_notice( 'php' ) && ! wie_show_security_notice( 'http' ) ) {
		return;
	}

	// Nonce.
	$ajax_nonce = wp_create_nonce( 'wie_dismiss_notice' );

	// JavaScript for detecting click on dismiss icon.
	?>

	<script type="text/javascript">

	jQuery( document ).ready( function( $ ) {

		// Dismiss icon
		$( document ).on( 'click', '#wie-security-notice .notice-dismiss', function() {

			// Notice container
			var $container = $( this ).parents( '#wie-security-notice' );

			// Get data-type attribute
			var type = $container.data( 'type' );

			// Send request.
			if ( 'php' === type || 'http' === type ) {

				$.ajax( {
					url: ajaxurl,
					data: {
						action: 'wie_dismiss_notice',
						security: '<?php echo esc_js( $ajax_nonce ); ?>',
						type: type,
					},
				} );

			}

		} );

		// Remind later link
		$( document ).on( 'click', '#wie-notice-remind-link', function() {

			// Stop click to URL.
			event.preventDefault();

			// Notice container
			var $container = $( this ).parents( '#wie-security-notice' );

			// Get data-type attribute
			var type = $container.data( 'type' );

			// Send request.
			if ( 'php' == type || 'http' == type ) {

				$.ajax( {
					url: ajaxurl,
					data: {
						action: 'wie_dismiss_notice',
						security: '<?php echo esc_js( $ajax_nonce ); ?>',
						type: type,
						reminder: true,
					},
				} );

			}

			// Fade out notice.
			$container.fadeOut( 'fast' );

		} );

	} );

	</script>

	<?php

}

// JavaScript for remembering notice was dismissed.
add_action( 'admin_print_footer_scripts', 'wie_dismiss_notice_js' );

/**
 * Set option to prevent notice from showing again.
 *
 * This is called by AJAX in wie_dismiss_notice_js()
 *
 * @since 1.5
 */
function wie_dismiss_notice() {

	// Only if is AJAX request.
	if ( ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
		return;
	}

	// Check nonce.
	check_ajax_referer( 'wie_dismiss_notice', 'security' );

	// Only if user is Administrator.
	if ( ! current_user_can( 'administrator' ) ) {
		return;
	}

	// Get type.
	if ( ! empty( $_GET['type'] ) && in_array( $_GET['type'], array( 'php', 'http' ), true ) ) {
		$type = wp_unslash( $_GET['type'] );
	} else {
		return;
	}

	// Option prefix.
	$option_prefix = 'wie_' . $type . '_notice';

	// Update option so notice is not shown again.
	if ( ! empty( $_GET['reminder'] ) ) {
		update_option( $option_prefix . '_reminder', current_time( 'timestamp' ) );
	} else {
		update_option( $option_prefix . '_dismissed', '1' );
	}

}

add_action( 'wp_ajax_wie_dismiss_notice', 'wie_dismiss_notice' );
