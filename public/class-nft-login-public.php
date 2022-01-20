<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://davehagler.github.io/nftlogin/
 * @since      1.0.0
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
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name      The name of the plugin.
	 * @param      string $plugin_prefix          The unique prefix of this plugin.
	 * @param      string $version          The version of this plugin.
	 */
	public function __construct( $plugin_name, $plugin_prefix, $version ) {

		$this->plugin_name   = $plugin_name;
		$this->plugin_prefix = $plugin_prefix;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/nft-login-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/nft-login-public.js', array( 'jquery' ), $this->version, true );

	}

	public function login_enqueue_scripts() {
        wp_enqueue_script( $this->plugin_name.'_web3', 'https://cdnjs.cloudflare.com/ajax/libs/web3/1.6.1/web3.min.js', null , '1.6.1', true );
        wp_enqueue_script( $this->plugin_name.'_nftlogin_module', plugin_dir_url( __FILE__ ) . 'js/nft-login-module.js', array( $this->plugin_name.'_web3' ), $this->version, true );
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

        if ($missing_address || $missing_token_id) {
            $errors->add('nft_owner_error', sprintf('<strong>%s</strong>: %s', __('Error'), __('Please verify nft ownership')));
        }

        return $errors;
    }

    public function authenticate( $user, $username, $password) {

	    $isAdministrator = in_array('administrator', $user->roles);
	    $isEmptyAddress = empty($_POST['nftlogin_address']) || !empty($_POST['nftlogin_address']) && trim($_POST['nftlogin_address']) == '';
        $isLoginError = $user instanceof WP_Error;

        if ($isLoginError || $isAdministrator) {
            return $user;
        }

        if ($isEmptyAddress) {
            $user  = new WP_Error( 'authentication_failed', __( 'ERROR: Please verify token ownership.' ) );
            return $user;
        }

        if(isset($user->ID)) {
            $user_meta = get_user_meta($user->ID);
            $addressMatch = $_POST['nftlogin_address'] == $user_meta['nftlogin_address'][0];
            $contractMatch = get_option('nft_login_setting_contract_address') == $user_meta['nftlogin_contract_address'][0];
            if (!$addressMatch || !$contractMatch) {
                $user  = new WP_Error( 'authentication_failed', __( 'ERROR: Verified NFT does not match registered user.' ) );
                return $user;
            }
        }

        return $user;
    }

    public function register_form() {

        $nftlogin_address = ( ! empty( $_POST['nftlogin_address'] ) ) ? sanitize_text_field( $_POST['nftlogin_address'] ) : '';
        $nftlogin_token_id = ( ! empty( $_POST['nftlogin_token_id'] ) ) ? sanitize_text_field( $_POST['nftlogin_token_id'] ) : '';
        $contract_address_setting = get_option('nft_login_setting_contract_address');
        $token_name_setting = get_option('nft_login_setting_token_name');
        ?>
        <p class="nftlogin_verify">
            <label>Verify ownership of <a href="#" onclick='var contractUrl="https://etherscan.io/token/<?= $contract_address_setting ?>";window.open(contractUrl, "_blank");'><?= $token_name_setting ?></a> NFT</label>
            <button class="button-secondary button" onclick="NFTLOGIN.connect_and_verify('<?= $contract_address_setting ?>');return false;">Verify</button>
        </p>
        <input type="hidden" id="nftlogin_address" name="nftlogin_address" value="<?php echo esc_attr(  $nftlogin_address  ); ?>"/>
        <input type="hidden" id="nftlogin_token_id" name="nftlogin_token_id" value="<?php echo esc_attr(  $nftlogin_token_id  ); ?>"/>
        <p id="nftlogin_status"></p>
        <br class="clear"/>

        <script type="text/javascript" src="https://unpkg.com/web3@1.2.11/dist/web3.min.js"></script>
        <script type="text/javascript" src="https://unpkg.com/web3modal@1.9.0/dist/index.js"></script>
        <script type="text/javascript" src="https://unpkg.com/evm-chains@0.2.0/dist/umd/index.min.js"></script>
        <script type="text/javascript" src="https://unpkg.com/@walletconnect/web3-provider@1.2.1/dist/umd/index.min.js"></script>
        <script type="text/javascript" src="https://unpkg.com/fortmatic@2.0.6/dist/fortmatic.js"></script>
        <?php
    }
}
