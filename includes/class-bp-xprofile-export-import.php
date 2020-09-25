<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       www.wbcomdesigns.com
 * @since      1.0.0
 *
 * @package    Bp_Xprofile_Export_Import
 * @subpackage Bp_Xprofile_Export_Import/includes
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
 * @since      1.0.0
 * @package    Bp_Xprofile_Export_Import
 * @subpackage Bp_Xprofile_Export_Import/includes
 * @author     Wbcom Designs <admin@gmail.com>
 */
class Bp_Xprofile_Export_Import {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Bp_Xprofile_Export_Import_Loader    $loader    Maintains and registers all hooks for the plugin.
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
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
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
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'bp-xprofile-export-import';
		$this->version     = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_admin_export_ajax_hooks();
		$this->define_admin_import_ajax_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Bp_Xprofile_Export_Import_Loader. Orchestrates the hooks of the plugin.
	 * - Bp_Xprofile_Export_Import_i18n. Defines internationalization functionality.
	 * - Bp_Xprofile_Export_Import_Admin. Defines all hooks for the admin area.
	 * - Bp_Xprofile_Export_Import_Public. Defines all hooks for the public side of the site.
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
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-bp-xprofile-export-import-loader.php';

		/**
		* The class responsible for defining internationalization functionality
		* of the plugin.
		*/
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-bp-xprofile-export-import-i18n.php';

		/**
		* The class responsible for defining all actions that occur in the admin area.
		*/
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-bp-xprofile-export-import-admin.php';

		/**
		* The class responsible for defining all actions for ajax that occur in the
		* admin area.
		*/
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-bp-xprofile-admin-export-ajax.php';

		/**
		* The class responsible for defining all actions for ajax that occur in the
		* admin area.
		*/
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-bp-xprofile-admin-import-ajax.php';

		$this->loader = new Bp_Xprofile_Export_Import_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Bp_Xprofile_Export_Import_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Bp_Xprofile_Export_Import_I18n();

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

		$plugin_admin = new Bp_Xprofile_Export_Import_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'bpxp_admin_menu' );

	}

	/**
	 * Register all of the hooks related to the admin area export member ajax
	 * functionality of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_export_ajax_hooks() {

		$export_admin = new Bp_Xprofile_Admin_Export_Ajax( $this->get_plugin_name(), $this->get_version() );

		/*	add action to set xprofile fields type */
		$this->loader->add_action( 'wp_ajax_bpxp_get_export_xprofile_fields', $export_admin, 'bpxp_get_xprofile_fields' );
		/* add action for exprot member data */
		$this->loader->add_action( 'wp_ajax_bpxp_export_xprofile_data', $export_admin, 'bpxp_export_member_data' );
		// $this->loader->add_action( 'wp_ajax_bpxp_export_xprofile_data', $export_admin, 'add_action( 'admin_init', 'bbg_csv_export' );');
	}

	/**
	 * Register all of the hooks related to the admin area export member ajax
	 * functionality of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_import_ajax_hooks() {

		$plugin_admin = new Bp_Xprofile_Admin_Import_Ajax( $this->get_plugin_name(), $this->get_version() );
		/* Ajax request for add maping fields */
		$this->loader->add_action( 'wp_ajax_bpxp_import_header_fields', $plugin_admin, 'bpxp_import_csv_header_fields' );
		/* import csv data */
		$this->loader->add_action( 'wp_ajax_bpxp_import_csv_data', $plugin_admin, 'bpxp_import_csv_member_data' );
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
	 * @return    Bp_Xprofile_Export_Import_Loader    Orchestrates the hooks of the plugin.
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
}
