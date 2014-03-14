<?php
/**
 * Adds the constants required for WordPress multisite to the defaults
 * provided in wp-config.php.
 *
 * These lines are provided uncommented, but will need to be commented
 * during installation. See the specific notes above each constant.
 */

/**
 * Comment the following WP_ALLOW_MULTISITE line with // while WordPress
 * is being installed. Once installed, uncomment this line to access
 * Tools -> Network Setup in the dashboard.
 */
define( 'WP_ALLOW_MULTISITE', true );

/**
 * Comment the following MULTISITE line with // during both the initial
 * WordPress installation and the Network Setup. Once the initial
 * Network Setup is complete, uncomment this line and reauthenticate.
 */
define( 'MULTISITE', true );
