<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://davehagler.github.io/nftlogin/
 *
 * @package    Nft_Login
 * @subpackage Nft_Login/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two hooks to
 * enqueue the public-facing stylesheet and JavaScript.
 * As you add hooks and methods, update this description.
 *
 * @package    Nft_Login
 * @subpackage Nft_Login/public
 * @author     Your Name <davehagler@gmail.com>
 */
class Nft_Login_Public {

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
     * @var boolean $is_content_verified Has content been verified
     */
	private $is_content_verified = false;

    private $utils;

    /**
	 * Initialize the class and set its properties.
	 *
	 * @param      string $plugin_name      The name of the plugin.
	 * @param      string $plugin_prefix          The unique prefix of this plugin.
	 * @param      string $version          The version of this plugin.
	 */
	public function __construct( $plugin_name, $plugin_prefix, $version ) {

		$this->plugin_name   = $plugin_name;
		$this->plugin_prefix = $plugin_prefix;
		$this->version = $version;
        $this->utils = new Nft_Login_Util();

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/nft-login-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 */
	public function enqueue_scripts() {
        wp_enqueue_script( $this->plugin_name.'_web3', plugin_dir_url( __FILE__ ) . 'js/web3-1.6.1.min.js', null , '1.6.1', true );
        wp_enqueue_script( $this->plugin_name.'_nftlogin_module', plugin_dir_url( __FILE__ ) . 'js/nft-login-module.js', array( $this->plugin_name.'_web3' ), $this->version, true );
        wp_enqueue_script( $this->plugin_name.'_evm_chains', plugin_dir_url( __FILE__ ) . 'js/evm-chains-0.2.0-index.min.js', array( $this->plugin_name.'_web3' ), $this->version, true );
        wp_enqueue_script( $this->plugin_name.'_formatic', plugin_dir_url( __FILE__ ) . 'js/formatic-2.0.6.js', array( $this->plugin_name.'_web3' ), $this->version, true );
        wp_enqueue_script( $this->plugin_name.'_web_provider', plugin_dir_url( __FILE__ ) . 'js/web3-provider-1.2.1-index.min.js', array( $this->plugin_name.'_web3' ), $this->version, true );
        wp_enqueue_script( $this->plugin_name.'_webmodal', plugin_dir_url( __FILE__ ) . 'js/webmodal-1.9.0-index.js', array( $this->plugin_name.'_web3' ), $this->version, true );
	}

	public function user_register($user_id) {
        if ( ! empty( $_POST['nftlogin_address'] ) ) {
            update_user_meta( $user_id, 'nftlogin_address', sanitize_text_field( $_POST['nftlogin_address'] ) );
        }
        if ( ! empty( $_POST['nftlogin_token_id'] ) ) {
            update_user_meta( $user_id, 'nftlogin_token_id', sanitize_text_field( $_POST['nftlogin_token_id'] ) );
        }
        update_user_meta( $user_id, 'nftlogin_contract_address', get_option('nft_login_setting_contract_address') );
	}

    public function registration_errors( $errors, $sanitized_user_login, $user_email ) {
        $missing_address = empty($_POST['nftlogin_address']) || !empty($_POST['nftlogin_address']) && trim($_POST['nftlogin_address']) == '';
        $missing_token_id = empty($_POST['nftlogin_token_id']) || !empty($_POST['nftlogin_token_id']) && trim($_POST['nftlogin_token_id']) == '';
        // skip verify missing token id because some contracts don't implement tokenOfOwnerByIndex
        if ($missing_address) {
            $errors->add('nft_owner_error', sprintf('<strong>%s</strong>: %s', __('Error'), __('Please verify nft ownership')));
        }

        return $errors;
    }

    public function authenticate( $user, $username, $password) {

	    if (isset($user->roles)) {
            $isAdministrator = in_array('administrator', $user->roles);
        }
	    $isEmptyAddress = empty($_POST['nftlogin_address']) || !empty($_POST['nftlogin_address']) && trim($_POST['nftlogin_address']) == '';
        $isLoginError = $user instanceof WP_Error;

        if ($isLoginError || $isAdministrator) {
            return $user;
        }

        if ($isEmptyAddress) {
            $user  = new WP_Error( 'authentication_failed', __( 'ERROR: Please verify token ownership.' ) );
            return $user;
        }

        return $user;
    }

    public function register_form() {

        $nftlogin_address = ( ! empty( $_POST['nftlogin_address'] ) ) ? sanitize_text_field( $_POST['nftlogin_address'] ) : '';
        $nftlogin_token_id = ( ! empty( $_POST['nftlogin_token_id'] ) ) ? sanitize_text_field( $_POST['nftlogin_token_id'] ) : '';
        $contract_address_setting = get_option('nft_login_setting_contract_address');
        $chain_id_setting = get_option('nft_login_setting_chain');
        // default for older versions of plugin
        if ($chain_id_setting == false) {
            $chain_id_setting = Nft_Login_Util::ETHEREUM_CHAIN_ID;
        }
        $token_name_setting = get_option('nft_login_setting_token_name');
        ?>
        <p class="nftlogin_verify">
            <button class="button-secondary button" onclick="NFTLOGIN.connect_and_verify('<?= $contract_address_setting ?>', null, '<?= $chain_id_setting ?>','<?= $this->utils->chain_id_to_name($chain_id_setting) ?>');return false;">Verify</button>
            <span> ownership of <?= $token_name_setting ?> NFT</span>
        </p>
        <input type="hidden" id="nftlogin_address" name="nftlogin_address" value="<?php echo esc_attr(  $nftlogin_address  ); ?>"/>
        <input type="hidden" id="nftlogin_token_id" name="nftlogin_token_id" value="<?php echo esc_attr(  $nftlogin_token_id  ); ?>"/>
        <p id="nftlogin_status"></p>
        <br class="clear"/>

        <?php
    }

    public function check_verified_content() {
        $cookie_name = 'nftlogin';
        $cookie_value = md5('nftlogin'. get_site_url());

        // already have unlock cookie
        if (isset($_COOKIE[$cookie_name]) && $_COOKIE[$cookie_name] == $cookie_value) {
            return;
        }

        // submitted verify request, set cookie and return content
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nftlogin_address = ( ! empty( $_POST['nftlogin_address'] ) ) ? sanitize_text_field( $_POST['nftlogin_address'] ) : '';
            $nftlogin_token_id = ( ! empty( $_POST['nftlogin_token_id'] ) ) ? sanitize_text_field( $_POST['nftlogin_token_id'] ) : '';
            // skip verification of nftlogin_token_id because some contracts don't implement tokenOfOwnerByIndex
            if ($nftlogin_address && $this->isValidAddress($nftlogin_address)) {
                setcookie($cookie_name, $cookie_value, strtotime('+1 day'));
                $this->is_content_verified = true;
                return;
            }
        }

    }

    public function protect_content($content) {
        global $post;
        if ($post->ID) {
            $nft_login_enabled = get_post_meta($post->ID, 'nft_login_enabled', true);
            if ($nft_login_enabled == 'true') {
                $contract_address_setting = get_option('nft_login_setting_contract_address');
                $token_name_setting = get_option('nft_login_setting_token_name');
                $chain_id_setting = get_option('nft_login_setting_chain');
                $cookie_name = 'nftlogin';
                $cookie_value = md5('nftlogin'. get_site_url());

                // already have unlock cookie, return the content
                if ($this->is_content_verified || (isset($_COOKIE[$cookie_name]) && $_COOKIE[$cookie_name] == $cookie_value)) {
                    return $content;
                }

                // show the verify form
                $login_content = '<form id="nftlogin_unlock_'.$post->ID.'" method="POST" >';
                $login_content .= '<p class="nftlogin_verify">';
                $login_content .= '<img style="padding:15px" height="60" width="60" src="'.plugin_dir_url( __FILE__ ) . 'image/lock.svg'.'" />';
                $login_content .= 'This content is protected. Please Verify NFT to view content.';
                $login_content .= '<button class="button-secondary button" onclick="NFTLOGIN.connect_and_verify(\'' . $contract_address_setting . '\', \'nftlogin_unlock_'.$post->ID.'\', \''.$chain_id_setting.'\',\''.$this->utils->chain_id_to_name($chain_id_setting).'\');return false;">Verify NFT</button>';
                $login_content .= '<input type="hidden" id="nftlogin_address" name="nftlogin_address" value=""/>';
                $login_content .= '<input type="hidden" id="nftlogin_token_id" name="nftlogin_token_id" value=""/>';
                $login_content .= '<input type="hidden" name="nftlogin_unlock" value="'.$post->ID.'"/>';
                $login_content .= '<div id="nftlogin_status"></div>';
                $login_content .= '</p>';
                $login_content .= '</form>';
                return $login_content;
            }
        }
        return $content;
    }

    // very simplistic check
    private function isValidAddress(string $address) {
        if (preg_match('/^0x[a-fA-F0-9]{40}$/', $address)) {
            if (preg_match('/^0x[a-f0-9]{40}$/', $address) || preg_match('/^0x[A-F0-9]{40}$/', $address)) {
                return true;
            }
        }

        return false;
    }

}
