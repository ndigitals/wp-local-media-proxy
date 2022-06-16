<?php
/**
 * Plugin Name: Local Development Media Library Proxy
 * Description: Provides the ability to proxy media library requests to a remote site for local development.
 *
 * @package  Ndigitals_Wp_Local_Media_Proxy_MuPlugin
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Check for remote media URL and add hooks to proxy media library images.
// Example Configuration "DDEV_USE_REMOTE_MEDIA_URL=https://example.com".
if ( defined( 'LOCALDEV_USE_REMOTE_MEDIA_URL' ) && ! empty( constant( LOCALDEV_USE_REMOTE_MEDIA_URL ) ) ) {
	add_filter( 'wp_get_attachment_image_src', 'wplmp_filter_wp_get_attachment_image_src' );
	add_filter( 'wp_calculate_image_srcset', 'wplmp_filter_wp_calculate_image_srcset' );
	add_filter( 'wp_get_attachment_url', 'wplmp_filter_wp_get_attachment_url' );
}

/**
 * Proxy requests for images to a remote URL.
 *
 * @link https://developer.wordpress.org/reference/hooks/wp_get_attachment_image_src/
 *
 * @param array|boolean $image Array of image data, or boolean false if no image is available.
 *
 * @return array|boolean
 */
function wplmp_filter_wp_get_attachment_image_src( $image = array() ) {

	if ( ! is_array( $image ) || empty( $image ) ) {
		return $image;
	}

	$wp_upload_dir = wp_upload_dir();
	$base_dir      = $wp_upload_dir['basedir'];
	$base_url      = $wp_upload_dir['baseurl'];
	$absolute_path = str_replace( $base_url, $base_dir, $image[0] );

	if ( file_exists( $absolute_path ) ) {
		return $image;
	}

	$find     = get_home_url();
	$replace  = LOCALDEV_USE_REMOTE_MEDIA_URL;
	$image[0] = str_replace( $find, $replace, $image[0] );

	return $image;

}

/**
 * Proxy srcset requests to a remote URL.
 *
 * @link https://developer.wordpress.org/reference/hooks/wp_calculate_image_srcset/
 *
 * @param array $sources One or more arrays of source data to include in the 'srcset'.
 *
 * @return array
 */
function wplmp_filter_wp_calculate_image_srcset( $sources = array() ) {

	if ( is_array( $sources ) && ! is_admin() ) {
		$wp_upload_dir = wp_upload_dir();
		$base_dir      = $wp_upload_dir['basedir'];
		$base_url      = $wp_upload_dir['baseurl'];
		$find          = get_home_url();
		$replace       = LOCALDEV_USE_REMOTE_MEDIA_URL;
		foreach ( $sources as $key => $val ) {
			$absolute_path = str_replace( $base_url, $base_dir, $val['url'] );

			if ( ! file_exists( $absolute_path ) ) {
				$val['url']  = str_replace( $find, $replace, $val['url'] );
				$sources[ $key ] = $val;
			}
		}
	}

	return $sources;

}

/**
 * Proxy the attachement URL to a remote site.
 *
 * @link https://developer.wordpress.org/reference/hooks/wp_get_attachment_url/
 *
 * @param string $url URL for the given attachment.
 *
 * @return string
 */
function wplmp_filter_wp_get_attachment_url( $url = '' ) {

	if ( is_admin() ) {
		return $url;
	}

	$wp_upload_dir = wp_upload_dir();
	$base_dir      = $wp_upload_dir['basedir'];
	$base_url      = $wp_upload_dir['baseurl'];
	$find          = get_home_url();
	$replace       = LOCALDEV_USE_REMOTE_MEDIA_URL;
	$absolute_path = str_replace( $base_url, $base_dir, $url );

	if ( ! file_exists( $absolute_path ) ) {
		$url = str_replace( $find, $replace, $url );
	}

	return $url;

}
