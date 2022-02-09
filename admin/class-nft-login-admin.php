<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://davehagler.github.io/nftlogin/
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
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The unique prefix of this plugin.
	 *
	 * @access   private
	 * @var      string    $plugin_prefix    The string used to uniquely prefix technical functions of this plugin.
	 */
	private $plugin_prefix;

	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

    /**
     * The options name to be used in this plugin
     *
     * @access 	private
     * @var  	string 		$option_name 	Option name of this plugin
     */
    private $option_name = 'nft_login_setting';

    /**
	 * Initialize the class and set its properties.
	 *
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
	 * @param string $hook_suffix The current admin page.
	 */
	public function enqueue_styles( $hook_suffix ) {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/nft-login-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @param string $hook_suffix The current admin page.
	 */
	public function enqueue_scripts( $hook_suffix ) {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/nft-login-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
     * Adds checkbox to posts and pages to lock content
     */
    public function add_meta_boxes($post_type, $post) {
        $screens = [ 'post', 'page' ];
        foreach ( $screens as $screen ) {
            add_meta_box(
                'nft_login_box_id',
                'NFT Content Protection',
                array($this, 'nft_login_meta_box_cb'),
                $screen,
                'side'
            );
        }
    }

    /**
     * Saves value of the content lock checkbox when post is saved
     * @param $post_id
     */
    public function save_post_meta_box($post_id) {
        if (wp_is_post_autosave($post_id) || wp_is_post_revision($post_id) ) {
            return;
        }
        if (isset($_POST['nft_login_enabled'])) {
            update_post_meta($post_id, 'nft_login_enabled', 'true');
        } else {
            update_post_meta($post_id, 'nft_login_enabled', 'false');
        }
    }

    /**
     * Register the setting parameters
     *
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

        // Configuration section
        add_settings_section(
            $this->option_name. '_configuration',
            __( 'Plugin Configuration', 'nft-login' ),
            array( $this, $this->option_name . '_configuration_cb' ),
            $this->plugin_name
        );
        add_settings_field(
            $this->option_name . '_reg_login',
            __('Registration and Login', 'nft-login'),
            array($this, $this->option_name . '_reg_login_cb'),
            $this->plugin_name,
            $this->option_name . '_configuration',
            array('label_for' => $this->option_name . '_reg_login')
        );
        register_setting($this->plugin_name,
            $this->option_name . '_reg_login',
            'string');

    }

    /**
     * Render the text for the address section
     *
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
    public function nft_login_setting_reg_login_cb() {
        $reg_login = get_option( $this->option_name . '_reg_login' );
        $checked = '';
        if ($reg_login == 'enabled') {
            $checked = "checked";
        }
        echo '<input type="checkbox" name="nft_login_setting_reg_login" id="nft_login_setting_reg_login" value="enabled" '.$checked.' />';
        echo '&nbsp;<label for="nft_login_setting_reg_login">Check this to require users to verify NFT ownership when registering/logging in to the site. Does not apply to admin users.</label>';
    }

    public function nft_login_meta_box_cb($post, $args) {
        $nft_login_enabled = get_post_meta($post->ID, 'nft_login_enabled', true);
    ?>
        <p>
            <input type="checkbox" name="nft_login_enabled" id="nft_login_enabled" value="1" <?php if ($nft_login_enabled == 'true') { echo "checked"; }  ?> />
            <label for="nft_login_enabled">Require NFT login</label>
        </p>
    <?php
    }
    /**
     * Include the setting page
     *
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

    /**
     * Add a column to the posts page to show if post is content locked
     */
    public function add_post_column($columns) {
        return array_merge($columns, ['nftlogin_login' => __('NFT Login', 'nft-login')]);
    }

    public function display_post_column($column_key, $post_id) {
        if ($column_key == 'nftlogin_login') {
            $nft_login_enabled = get_post_meta($post_id, 'nft_login_enabled', true);
            if ($nft_login_enabled == 'true') {
                _e('Content locked', 'nft-login');
            }
        }
    }
}
