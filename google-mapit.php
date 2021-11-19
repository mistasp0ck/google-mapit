<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              tonystaffiero.com
 * @since             1.0.0
 * @package           Google_Mapit
 *
 * @wordpress-plugin
 * Plugin Name:       Google MapIt
 * Plugin URI:        tonystaffiero.com/mapit
 * Description:       Enables you to add locations to a custom google map and display it with the shortcode [map]
 * Version:           1.3.0
 * Author:            Tony Staffiero
 * Author URI:        tonystaffiero.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       google-mapit
 * Domain Path:       /languages
 */

define( 'GMI_PLUGIN', __FILE__ );

define( 'GMI_PLUGIN_BASENAME', plugin_basename( GMI_PLUGIN ) );

define( 'GMI_PLUGIN_NAME', trim( dirname( GMI_PLUGIN_BASENAME ), '/' ) );

define( 'GMI_PLUGIN_DIR', untrailingslashit( dirname( GMI_PLUGIN ) ) );

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-google-mapit-activator.php
 */
function activate_google_mapit() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-google-mapit-activator.php';
	Google_Mapit_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-google-mapit-deactivator.php
 */
function deactivate_google_mapit() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-google-mapit-deactivator.php';
	Google_Mapit_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_google_mapit' );
register_deactivation_hook( __FILE__, 'deactivate_google_mapit' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-google-mapit.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_google_mapit() {

	$plugin = new Google_Mapit();
	$plugin->run();

}
run_google_mapit();
