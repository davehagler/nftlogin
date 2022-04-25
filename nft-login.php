<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress or ClassicPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://davehagler.github.io/nftlogin/
 * @package           Nft_Login
 *
 * @wordpress-plugin
 * Plugin Name:       NFT Login
 * Plugin URI:        https://davehagler.github.io/nftlogin/
 * Description:       Login to Wordpress using NFT's
 * Version:           1.2.4
 * Author:            Dave Hagler
 * Requires at least: 5.0
 * Tested up to:      5.8
 * License:           GPL-2.0+
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       nft-login
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Current plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'NFT_LOGIN_VERSION', '1.2.4' );

!defined('NFT_LOGIN_PATH') && define('NFT_LOGIN_PATH', plugin_dir_path( __FILE__ ));

/**
 * The code that runs during plugin activation.
 *
 * This action is documented in includes/class-nft-login-activator.php
 * Full security checks are performed inside the class.
 */
function nft_login_activate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-nft-login-activator.php';
	Nft_Login_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 *
 * This action is documented in includes/class-nft-login-deactivator.php
 * Full security checks are performed inside the class.
 */
function nft_login_deactivate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-nft-login-deactivator.php';
	Nft_Login_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'nft_login_activate' );
register_deactivation_hook( __FILE__, 'nft_login_deactivate' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-nft-login.php';

/**
 * Begins execution of the plugin.
 *
 */
function nft_login_run() {

	$plugin = new Nft_Login();
	$plugin->run();

}
nft_login_run();
