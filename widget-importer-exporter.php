<?php
/**
 * Plugin Name: Widget Importer & Exporter
 * Plugin URI: https://wpultimate.com/widget-importer-exporter
 * Description: Imports and exports widgets.
 * Version: 1.5
 * Author: WP Ultimate
 * Author URI: https://wpultimate.com
 * License: GPLv2 or later
 * Text Domain: widget-importer-exporter
 * Domain Path: /languages
 *
 * @package   Widget_Importer_Exporter
 * @copyright Copyright (c) 2013 - 2017, WP Ultimate
 * @link      https://wpultimate.com/widget-importer-exporter
 * @license   GPLv2 or later
 */

// No direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main class
 *
 * @since 0.1
 */
class Widget_Importer_Exporter {

	/**
	 * Plugin data from get_plugins()
	 *
	 * @since 0.1
	 * @var object
	 */
	public $plugin_data;

	/**
	 * Includes to load
	 *
	 * @since 0.1
	 * @var array
	 */
	public $includes;

	/**
	 * Constructor
	 *
	 * Add actions for methods that define constants, load translation and load includes.
	 *
	 * @since  0.1
	 * @access public
	 */
	public function __construct() {

		// Set plugin data.
		add_action( 'plugins_loaded', array( &$this, 'set_plugin_data' ), 1 );
		// Define constants.
		add_action( 'plugins_loaded', array( &$this, 'define_constants' ), 1 );
		// Load language file.
		add_action( 'plugins_loaded', array( &$this, 'load_textdomain' ), 1 );
		// Set includes.
		add_action( 'plugins_loaded', array( &$this, 'set_includes' ), 1 );
		// Load includes.
		add_action( 'plugins_loaded', array( &$this, 'load_includes' ), 1 );
	}

	/**
	 * Set plugin data
	 *
	 * This data is used by constants.
	 *
	 * @since  0.1
	 * @access public
	 */
	public function set_plugin_data() {

		// Load plugin.php if get_plugins() not available.
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		// Get path to plugin's directory.
		$plugin_dir = plugin_basename( dirname( __FILE__ ) );

		// Get plugin data.
		$plugin_data = current( get_plugins( '/' . $plugin_dir ) );

		// Set plugin data.
		$this->plugin_data = apply_filters( 'wie_plugin_data', $plugin_data );
	}

	/**
	 * Define constants
	 *
	 * @since  0.1
	 * @access public
	 */
	public function define_constants() {

		// Plugin version.
		define( 'WIE_VERSION', $this->plugin_data['Version'] );
		// Plugin's main file path.
		define( 'WIE_FILE', __FILE__ );
		// Plugin's directory.
		define( 'WIE_DIR', dirname( plugin_basename( WIE_FILE ) ) );
		// Plugin's directory path.
		define( 'WIE_PATH', untrailingslashit( plugin_dir_path( WIE_FILE ) ) );
		// Plugin's directory URL.
		define( 'WIE_URL', untrailingslashit( plugin_dir_url( WIE_FILE ) ) );
		// Includes directory.
		define( 'WIE_INC_DIR', 'includes' );
		// Stylesheets directory.
		define( 'WIE_CSS_DIR', 'css' );
		// Image directory.
		define( 'WIE_IMG_DIR', 'img' );
		// Languages directory.
		define( 'WIE_LANG_DIR', 'languages' );

	}

	/**
	 * Load language file
	 *
	 * This will load the MO file for the current locale.
	 * The translation file must be named widget-importer-exporter-$locale.mo.
	 *
	 * First it will check to see if the MO file exists in wp-content/languages/plugins.
	 * If not, then the 'languages' directory inside the plugin will be used.
	 * It is ideal to keep translation files outside of the plugin to avoid loss during updates.\
	 *
	 * @since  0.1
	 * @access public
	 */
	public function load_textdomain() {

		// Text-domain.
		$domain = 'widget-importer-exporter';

		// WordPress core locale filter.
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		// WordPress 3.6 and earlier don't auto-load from wp-content/languages, so check and load manually
		// http://core.trac.wordpress.org/changeset/22346.
		$external_mofile = WP_LANG_DIR . '/plugins/' . $domain . '-' . $locale . '.mo';

		// External translation exists.
		if ( get_bloginfo( 'version' ) <= 3.6 && file_exists( $external_mofile ) ) {
			load_textdomain( $domain, $external_mofile );
		} else {

			// Load normally. Either using WordPress 3.7+ or older version with external translation.
			$languages_dir = WIE_DIR . '/' . trailingslashit( WIE_LANG_DIR ); // ensure trailing slash.
			load_plugin_textdomain( $domain, false, $languages_dir );

		}

	}

	/**
	 * Set includes
	 *
	 * @since  0.1
	 * @access public
	 */
	public function set_includes() {

		$this->includes = apply_filters( 'wie_includes', array(
			// Admin only.
			'admin' => array(
				// Functions.
				WIE_INC_DIR . '/admin.php',
				WIE_INC_DIR . '/export.php',
				WIE_INC_DIR . '/import.php',
				WIE_INC_DIR . '/mime-types.php',
				WIE_INC_DIR . '/notices.php',
				WIE_INC_DIR . '/page.php',
				WIE_INC_DIR . '/widgets.php',
			),
		) );
	}

	/**
	 * Load includes
	 *
	 * Include files based on whether or not condition is met.
	 *
	 * @since  0.1
	 * @access public
	 */
	public function load_includes() {

		// Get includes.
		$includes = $this->includes;

		// Loop conditions.
		foreach ( $includes as $condition => $files ) {
			$do_includes = false;

			// Check condition.
			switch ( $condition ) {
				// Admin Only.
				case 'admin':
					if ( is_admin() ) {
						$do_includes = true;
					}
					break;

				// Frontend Only.
				case 'frontend':
					if ( ! is_admin() ) {
						$do_includes = true;
					}
					break;

				// Admin or Frontend (always).
				default:
					$do_includes = true;
					break;
			}

			// Loop files if condition met.
			if ( $do_includes ) {
				foreach ( $files as $file ) {
					require_once trailingslashit( WIE_PATH ) . $file;
				}
			}
		}
	}
}

// Instantiate the main class.
new Widget_Importer_Exporter();
