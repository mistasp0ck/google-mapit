<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       tonystaffiero.com
 * @since      1.0.0
 *
 * @package    Google_Mapit
 * @subpackage Google_Mapit/includes
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
 * @since      1.1.0
 * @package    Google_Mapit
 * @subpackage Google_Mapit/includes
 * @author     Tony Staffiero <me@tonystaffiero.com>
 */
class Google_Mapit {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Google_Mapit_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * Prefix for Settings/Meta Fields.
	 *
	 * @since    1.2.0
	 * @access   protected
	 * @var      string    $prefix    Prefix for Settings/Meta Fields.
	 */
	protected $prefix;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * The taxonomy name for locations.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	// protected $taxonomy;	

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'google-maps-locator';
		$this->version = '1.2.0';
		$this->prefix = 'gmi_';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

		// $this->taxonomy = "location-category";

		

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Google_Mapit_Loader. Orchestrates the hooks of the plugin.
	 * - Google_Mapit_i18n. Defines internationalization functionality.
	 * - Google_Mapit_Admin. Defines all hooks for the admin area.
	 * - Google_Mapit_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-google-mapit-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-google-mapit-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-google-mapit-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-google-mapit-public.php';

		$this->loader = new Google_Mapit_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Google_Mapit_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Google_Mapit_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		// $taxonomy = $this->get_taxonomy();

		$plugin_admin = new Google_Mapit_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// Add our locations post type and meta boxes
		$this->loader->add_action( 'init', $plugin_admin, 'create_locations_posttype', 9 );
		$this->loader->add_action( 'add_meta_boxes_location', $plugin_admin, 'google_mapit_add_location_meta_box' );
		$this->loader->add_action( 'save_post_location', $plugin_admin, 'google_mapit_save_location_meta' );

		$this->loader->add_action( 'admin_menu', $plugin_admin, 'google_mapit_add_admin_menu' );
		// $this->loader->add_action( 'admin_init', $plugin_admin, 'google_mapit_settings_init' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'setup_sections' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'google_mapit_setup_fields' );		
		// $this->loader->add_action( 'admin_init', $plugin_admin, 'google_mapit_design_options_init' );

		// $this->loader->add_action( 'locationcategory_add_form_fields', $plugin_admin, 'google_mapit_add_location_category_meta_box', 10, 2 );

		$this->loader->add_action( 'init', $plugin_admin, 'location_category_register_meta' );


		// $this->loader->add_action("location-category_add_form_fields", $plugin_admin, 'add_taxonomy_field');
		// $this->loader->add_action("location-category_edit_form_fields", $plugin_admin, 'edit_taxonomy_field');


		$this->loader->add_action( 'location-category_add_form_fields', $plugin_admin, 'google_mapit_add_location_category_icon' );

		$this->loader->add_action( 'location-category_edit_form_fields', $plugin_admin, 'google_mapit_add_location_category_icon' );

		$this->loader->add_action( 'edit_location-category', $plugin_admin, 'google_mapit_save_location_category_icon' );
		$this->loader->add_action( 'create_location-category', $plugin_admin, 'google_mapit_save_location_category_icon' );
		
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Google_Mapit_Public( $this->get_plugin_name(), $this->get_version(), $this->get_prefix() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		$this->loader->add_action('wp_print_footer_scripts', $plugin_public, 'enqueue_footer_scripts');

		$this->loader->add_action('rest_api_init', $plugin_public, 'google_mapit_add_rest_meta');

		$this->loader->add_action( 'init', $plugin_public, 'google_mapit_shortcode_init');

		$this->loader->add_action( 'shutdown', $plugin_public, 'shutdown');

		// $this->loader->add_filter('shortcode_atts_map', $plugin_public, 'get_google_mapit_shortcode_atts');

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Google_Mapit_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_taxonomy() {
		return $this->taxonomy;
	}

	/**
	 * Retrieve the prefix for the plugin.
	 *
	 * @since     1.2.0
	 * @return    string    The prefix for the plugin.
	 */
	public function get_prefix() {
		return $this->prefix;
	}


}