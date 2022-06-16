# WP Local Media Proxy
Proxy images on a local development WordPress site from a remote server.

## Documentation

### Installation

#### Via Composer

1. Add the package as a Development dependency. `composer require --dev ndigitals/wp-local-media-proxy`.

#### Manually

1. Download the main plugin file, `wp-local-media-proxy.php` to your local machine.
2. Upload the main plugin file to the `wp-content/mu-plugins/` directory on your site.

### Configuration

You will need to define the constant `LOCALDEV_USE_REMOTE_MEDIA_URL` either in your `wp-config.php` file or in your `.env` file when using the [PHP dotenv](https://github.com/vlucas/phpdotenv) package.
