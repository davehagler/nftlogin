<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://davehagler.github.io/nftlogin/
 *
 * @package    Nft_Login
 * @subpackage Nft_Login/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @package    Nft_Login
 * @subpackage Nft_Login/includes
 * @author     Your Name <davehagler@gmail.com>
 */
class Nft_Login {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @access   protected
	 * @var      Nft_Login_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The unique prefix of this plugin.
	 *
	 * @access   protected
	 * @var      string    $plugin_prefix    The string used to uniquely prefix technical functions of this plugin.
	 */
	protected $plugin_prefix;

	/**
	 * The current version of the plugin.
	 *
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 */
	public function __construct() {

		if ( defined( 'NFT_LOGIN_VERSION' ) ) {

			$this->version = NFT_LOGIN_VERSION;

		} else {

			$this->version = '1.0.0';

		}

		$this->plugin_name = 'nft-login';
		$this->plugin_prefix = 'nft_login_';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Nft_Login_Loader. Orchestrates the hooks of the plugin.
	 * - Nft_Login_i18n. Defines internationalization functionality.
	 * - Nft_Login_Admin. Defines all hooks for the admin area.
	 * - Nft_Login_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-nft-login-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-nft-login-i18n.php';

        /**
         * Utilities
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-nft-login-util.php';

        /**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-nft-login-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-nft-login-public.php';

		$this->loader = new Nft_Login_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Nft_Login_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Nft_Login_I18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Nft_Login_Admin( $this->get_plugin_name(), $this->get_plugin_prefix(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

        $this->loader->add_action('admin_init', $plugin_admin, 'register_plugin_settings');
		$this->loader->add_action('admin_menu', $plugin_admin, 'add_menu_page');
		$this->loader->add_action('add_meta_boxes', $plugin_admin,'add_meta_boxes', 10, 2);
        $this->loader->add_action('save_post', $plugin_admin,'save_post_meta_box', 10, 1);

        $this->loader->add_filter('manage_post_posts_columns', $plugin_admin, 'add_post_column',30, 1);
        $this->loader->add_action('manage_post_posts_custom_column', $plugin_admin, 'display_post_column',30, 2);
        $this->loader->add_filter('manage_page_posts_columns', $plugin_admin, 'add_post_column',30, 1);
        $this->loader->add_action('manage_page_posts_custom_column', $plugin_admin, 'display_post_column',30, 2);
	}

    /**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Nft_Login_Public( $this->get_plugin_name(), $this->get_plugin_prefix(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
        $this->loader->add_action( 'login_enqueue_scripts', $plugin_public, 'enqueue_styles' );
        $this->loader->add_action( 'login_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

        if (get_option( 'nft_login_setting_reg_login' ) == 'enabled') {
            // registration hooks
            $this->loader->add_action('register_form', $plugin_public, 'register_form');
            $this->loader->add_action('user_register', $plugin_public, 'user_register');
            $this->loader->add_filter('registration_errors', $plugin_public, 'registration_errors', 10, 3);

            // login hooks
            $this->loader->add_action('login_form', $plugin_public, 'register_form');
            $this->loader->add_filter('authenticate', $plugin_public, 'authenticate', 30, 3);
        }

        // protected content hooks
        $this->loader->add_action('wp_loaded', $plugin_public, 'check_verified_content');
        $this->loader->add_filter('the_content', $plugin_public, 'protect_content');
        $this->loader->add_filter('the_excerpt', $plugin_public, 'protect_content');
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The unique prefix of the plugin used to uniquely prefix technical functions.
	 *
	 * @return    string    The prefix of the plugin.
	 */
	public function get_plugin_prefix() {
		return $this->plugin_prefix;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @return    Nft_Login_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
