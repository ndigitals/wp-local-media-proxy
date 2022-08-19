<?php
/**
 * Main plugin class.
 *
 * @package  Ndigitals_Wp_Local_Media_Proxy_MuPlugin
 *
 * @link https://gist.github.com/kingkool68/d5e483528a260e5c7921afb5c88bffd6
 */

namespace Ndigitals\MuPlugin;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Wp_Local_Media_Proxy class.
 * Defines plugin functionality.
 *
 * @package Ndigitals_Wp_Local_Media_Proxy_MuPlugin
 */
class Wp_Local_Media_Proxy {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since 1.1.0
	 *
	 * @var string
	 */
	const VERSION = '1.1.1';

	/**
	 * Instance of this class.
	 *
	 * @since 1.1.0
	 *
	 * @var Wp_Local_Media_Proxy
	 */
	protected static $_instance = null;

	/**
	 * The home URL of the site to use for images/media. Default: ''.
	 *
	 * @since 1.1.0
	 *
	 * @var string
	 */
	protected $home_url = '';

	/**
	 * The URL of the remote site to use for images/media. Default: ''.
	 *
	 * @since 1.1.0
	 *
	 * @var string
	 */
	protected $remote_medial_url = '';

	/**
	 * The WordPress uploads directory path. Default: ''.
	 *
	 * @since 1.1.0
	 *
	 * @var string
	 */
	protected $wp_upload_base_dir = '';

	/**
	 * The WordPress uploads directory URL. Default: ''.
	 *
	 * @since 1.1.0
	 *
	 * @var string
	 */
	protected $wp_upload_base_url = '';

	/**
	 * Initialize the plugin by setting localization and loading public scripts,
	 * styles, filters, and hooks.
	 *
	 * @since 1.1.0
	 */
	private function __construct() {

		// Make sure the home URL contains a trailing slash for consistent find/replace actions.
		$this->home_url = trailingslashit( get_home_url() );

		// Check for remote media URL and set the class property.
		// Example Configuration "LOCALDEV_USE_REMOTE_MEDIA_URL=https://example.com".
		if ( defined( 'LOCALDEV_USE_REMOTE_MEDIA_URL' ) && ! empty( constant( 'LOCALDEV_USE_REMOTE_MEDIA_URL' ) ) ) {
			// Make sure the remote URL contains a trailing slash for consistent find/replace actions.
			$this->remote_medial_url = trailingslashit( LOCALDEV_USE_REMOTE_MEDIA_URL );
		}

		// Store the WordPress uploads directory path & URL for image/media replacements.
		$wp_upload_dir            = wp_upload_dir();
		$this->wp_upload_base_dir = $wp_upload_dir['basedir'];
		$this->wp_upload_base_url = $wp_upload_dir['baseurl'];

		// Register hooks.
		$this->register_hooks();

	}

	/**
	 * Return an instance of this class.
	 *
	 * @since 1.1.0
	 *
	 * @return Wp_Local_Media_Proxy    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null === self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Register any required hooks for MU Plugin functionality.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	private function register_hooks() {

		// Check for remote media URL and add hooks to proxy media library images.
		if ( ! empty( $this->remote_medial_url ) ) {
			// Add filters only if constant is configured.
			add_filter( 'wp_get_attachment_image_src', array( $this, 'filter_wp_get_attachment_image_src' ) );
			add_filter( 'wp_calculate_image_srcset', array( $this, 'filter_wp_calculate_image_srcset' ) );
			add_filter( 'wp_get_attachment_url', array( $this, 'filter_wp_get_attachment_url' ) );
		}

	}

	/**
	 * Check for a local media file and if it doesn't exist replace the home URL with the remote URL.
	 *
	 * @since 1.1.0
	 *
	 * @param string $image_url The URL of the image/media file.
	 *
	 * @return string
	 */
	private function replace_url( $image_url ) {

		$absolute_path = str_replace( $this->wp_upload_base_url, $this->wp_upload_base_dir, $image_url );

		if ( file_exists( $absolute_path ) ) {
			return $image_url;
		}

		$image_url = str_replace( $this->home_url, $this->remote_medial_url, $image_url );

		return $image_url;

	}

	/**
	 * Proxy requests for images to a remote URL.
	 *
	 * @link https://developer.wordpress.org/reference/hooks/wp_get_attachment_image_src/
	 *
	 * @since 1.1.0
	 *
	 * @param array|boolean $image Array of image data, or boolean false if no image is available.
	 *
	 * @return array|boolean
	 */
	public function filter_wp_get_attachment_image_src( $image = array() ) {

		if ( ! is_array( $image ) || empty( $image ) ) {
			return $image;
		}

		$image[0] = $this->replace_url( $image[0] );

		return $image;

	}

	/**
	 * Proxy srcset requests to a remote URL.
	 *
	 * @link https://developer.wordpress.org/reference/hooks/wp_calculate_image_srcset/
	 *
	 * @since 1.1.0
	 *
	 * @param array $sources One or more arrays of source data to include in the 'srcset'.
	 *
	 * @return array
	 */
	public function filter_wp_calculate_image_srcset( $sources = array() ) {

		if ( is_array( $sources ) && ! is_admin() ) {
			foreach ( $sources as $key => $val ) {
				$val['url']  = $this->replace_url( $val['url'] );
				$sources[ $key ] = $val;
			}
		}

		return $sources;

	}

	/**
	 * Proxy the attachement URL to a remote site.
	 *
	 * @link https://developer.wordpress.org/reference/hooks/wp_get_attachment_url/
	 *
	 * @since 1.1.0
	 *
	 * @param string $url URL for the given attachment.
	 *
	 * @return string
	 */
	public function filter_wp_get_attachment_url( $url = '' ) {

		if ( empty( $url ) ) {
			return $url;
		}

		return $this->replace_url( $url );

	}

}
