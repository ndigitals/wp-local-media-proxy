<?php
/**
 * Phpstan bootstrap file.
 *
 * @package   Ndigitals_Wp_Local_Media_Proxy_MuPlugin
 * @author    Tim Nolte <tim.nolte@ndigitals.com>
 * @copyright 2022 timnolte
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt GPL-2.0+
 * @link      https://github.com/ndigitals/wp-local-media-proxy
 */

// Define Plugin Globals.
defined( 'LOCALDEV_USE_REMOTE_MEDIA_URL' ) || define( 'LOCALDEV_USE_REMOTE_MEDIA_URL', 'http://' . bin2hex( random_bytes( 32 ) ) . '.test' );
