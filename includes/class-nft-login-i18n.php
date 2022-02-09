<?php
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://davehagler.github.io/nftlogin/
 *
 * @package    Nft_Login
 * @subpackage Nft_Login/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 *
 * @package    Nft_Login
 * @subpackage Nft_Login/includes
 * @author     Your Name <davehagler@gmail.com>
 */
class Nft_Login_I18n {

	/**
	 * Load the plugin text domain for translation.
	 *
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'nft-login',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
