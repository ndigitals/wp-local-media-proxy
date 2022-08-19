<?php
/**
 * Plugin Name: Local Development Media Library Proxy
 * Description: Provides the ability to proxy media library requests to a remote site for local development.
 * Version: 1.1.1
 * Author: Tim Nolte
 * Author URI: https://www.ndigitals.com
 *
 * @package  Ndigitals_Wp_Local_Media_Proxy_MuPlugin
 *
 * @link https://gist.github.com/kingkool68/d5e483528a260e5c7921afb5c88bffd6
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Require public facing functionality
 */
require_once( plugin_dir_path( __FILE__ ) . 'class-wp-local-media-proxy.php' );

use Ndigitals\MuPlugin\Wp_Local_Media_Proxy;

// Due to namespace usage and composer package requirements we require PHP >= 7.0.0.
if ( ! defined( 'PHP_VERSION' ) || ! function_exists( 'version_compare' ) || version_compare( PHP_VERSION, '7.0.0', '<' ) ) {

	// When plugin is used via the WP-CLI print out the text instead of expecting the WordPress Dashboard to be present.
	if ( class_exists( 'WP_CLI' ) ) {

		WP_CLI::warning( _wplmp_php_version_text() );

	} else {

		// Add Dashboard admin notice for plugin PHP version check failure.
		add_action( 'admin_notices', '_wplmp_php_version_error' );

	}
} else {

	// Startup the plugin to register hooks.
	_wplmp_init();

}

/**
 * Admin notice for incompatible versions of PHP.
 *
 * @since 1.1.0
 */
function _wplmp_php_version_error() {

	printf( '<div class="error"><p>%s</p></div>', esc_html( _wplmp_php_version_text() ) );

}

/**
 * String describing the minimum PHP version.
 *
 * @since 1.1.0
 *
 * @return string The localized PHP version requirement message text.
 */
function _wplmp_php_version_text() {

	return __( 'Local Media Proxy MU Plugin error: Your version of PHP is too old to run this MU Plugin. You must be running PHP 7.0 or higher.', 'wp-local-media-proxy' );

}

/**
 * Intializes the main plugin global instance.
 *
 * @since 1.1.0
 *
 * @return Ndigitals\MuPlugin\Wp_Local_Media_Proxy The single Ndigitals\MuPlugin\Wp_Local_Media_Proxy instance.
 */
function _wplmp_init() {

	$muplugin = Wp_Local_Media_Proxy::get_instance();

	return $muplugin;

}
