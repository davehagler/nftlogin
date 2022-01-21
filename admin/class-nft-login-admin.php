<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://davehagler.github.io/nftlogin/
 * @since      1.0.0
 *
 * @package    Nft_Login
 * @subpackage Nft_Login/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two hooks to
 * enqueue the admin-facing stylesheet and JavaScript.
 * As you add hooks and methods, update this description.
 *
 * @package    Nft_Login
 * @subpackage Nft_Login/admin
 * @author     Your Name <davehagler@gmail.com>
 */
class Nft_Login_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The unique prefix of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_prefix    The string used to uniquely prefix technical functions of this plugin.
	 */
	private $plugin_prefix;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

    /**
     * The options name to be used in this plugin
     *
     * @since  	1.0.0
     * @access 	private
     * @var  	string 		$option_name 	Option name of this plugin
     */
    private $option_name = 'nft_login_setting';

    /**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $plugin_prefix    The unique prefix of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $plugin_prefix, $version ) {

		$this->plugin_name   = $plugin_name;
		$this->plugin_prefix = $plugin_prefix;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 * @param string $hook_suffix The current admin page.
	 */
	public function enqueue_styles( $hook_suffix ) {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/nft-login-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 * @param string $hook_suffix The current admin page.
	 */
	public function enqueue_scripts( $hook_suffix ) {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/nft-login-admin.js', array( 'jquery' ), $this->version, false );

	}

    /**
     * Register the setting parameters
     *
     * @since  	1.0.0
     * @access 	public
     */
    public function register_plugin_settings() {
        // Add a Contract Address section
         add_settings_section(
            $this->option_name. '_address',
            __( 'Token Settings', 'nft-login' ),
            array( $this, $this->option_name . '_address_cb' ),
            $this->plugin_name
        );
        add_settings_field(
            $this->option_name . '_token_name',
            __('Token Name', 'nft-login'),
            array($this, $this->option_name . '_token_name_cb'),
            $this->plugin_name,
            $this->option_name . '_address',
            array('label_for' => $this->option_name . '_token_name')
        );
        add_settings_field(
            $this->option_name . '_contract_address',
            __('Contract Address', 'nft-login'),
            array($this, $this->option_name . '_contract_address_cb'),
            $this->plugin_name,
            $this->option_name . '_address',
            array('label_for' => $this->option_name . '_contract_address')
        );
        register_setting($this->plugin_name,
            $this->option_name . '_token_name',
            'string');
        register_setting($this->plugin_name,
            $this->option_name . '_contract_address',
            'string');
    }

    /**
     * Render the text for the address section
     *
     * @since  	1.0.0
     * @access 	public
     */
    public function nft_login_setting_address_cb() {
        $contractsFile = plugin_dir_path( dirname( __FILE__ ) ) . 'resources/ethereum-contract-addresses.csv';
        $contractAddresses = array_map('str_getcsv', file($contractsFile));
        echo '<select name="nft_login_setting_contracts" id="nft_login_setting_contracts" onchange="">';
        echo '<option value="" selected>Choose one or enter your own</option>';
        foreach($contractAddresses as $contractAddress) {
            echo '<option value="'.esc_attr($contractAddress[1]).'">'.esc_html($contractAddress[0]).'</option>';
        }
        echo '</select>';

    }
    public function nft_login_setting_token_name_cb() {
        $val = get_option( $this->option_name . '_token_name' );
        echo '<input type="text" size="56" name="nft_login_setting_token_name' . '" id="nft_login_setting_token_name' . '" value="' . esc_attr($val) . '"> ' ;
    }
    public function nft_login_setting_contract_address_cb() {
        $val = get_option( $this->option_name . '_contract_address' );
        echo '<input type="text" size="56" name="nft_login_setting_contract_address' . '" id="nft_login_setting_contract_address' . '" value="' . esc_attr($val) . '"> ' ;
        echo '<a href="#" onclick=\'var contractUrl="https://etherscan.io/token/"+document.getElementById("nft_login_setting_contract_address").value;window.open(contractUrl, "_blank");\'>View contract on Etherscan.io</a>';

    }

    /**
     * Include the setting page
     *
     * @since  1.0.0
     * @access public
     */
    function nft_login_init(){
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        include NFT_LOGIN_PATH . 'admin/partials/nft-login-admin-display.php' ;
    }

    public function add_menu_page() {
        add_menu_page('NFT Login Plugin', 'NFT Login Plugin', 'manage_options', 'nft_login_plugin', array($this,'nft_login_init'));
    }

}
