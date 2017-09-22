<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              www.wbcomdesigns.com
 * @since             1.0.0
 * @package           Bp_Xprofile_Export_Import
 *
 * @wordpress-plugin
 * Plugin Name:       Buddypress Member Export Import
 * Plugin URI:        https://wbcomdesigns.com/contact/
 * Description:       Buddypress Member Export Import plugin bring you feature to export Buddypress members and x-profile fields data into CSV file and import buddypress members from CSV file.
 
 * Version:           1.0.0
 * Author:            Wbcom Designs
 * Author URI:        www.wbcomdesigns.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bp-xprofile-export-import
 * Domain Path:       /languages
 */


if (!defined('ABSPATH')) exit; // Exit if accessed directly

	/**
	* Constants used in the plugin
	*/
	define('BPXP_PLUGIN_PATH', plugin_dir_path(__FILE__));
	define('BPXP_PLUGIN_URL', plugin_dir_url(__FILE__));
	define('BPXP_TEXT_DOMAIN', 'bp-xprofile-export-import');
	
if ( !function_exists( 'bpxp_plugins_files' ) ) {

	add_action( 'plugins_loaded', 'bpxp_plugins_files' );

	/**
	* Include requir files
	*
	* @author 	Wbcom Designs
	* @since    1.0.0
	*/
	function bpxp_plugins_files(){
		if (!in_array('buddypress/bp-loader.php', apply_filters('active_plugins', get_option('active_plugins')))) {
			add_action( 'admin_notices', 'bpxp_admin_notice' );
		}else{
			bpxp_run_bp_xprofile_export_import();
		}
	}
}

if ( !function_exists( 'bpxp_admin_notice' ) ) {
	/**
	* Display admin notice
	*
	* @author 	Wbcom Designs
	* @since    1.0.0
	*/
	function bpxp_admin_notice() {
	    ?>
	    <div class="error notice is-dismissible">
	        <p><?php _e( 'The <b>Buddypress Member Export Import </b> plugin requires <b>Buddypress</b> plugin to be installed and active', BPXP_TEXT_DOMAIN ); ?></p>
	    </div>
	    <?php
	}
}

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-bp-xprofile-export-import.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function bpxp_run_bp_xprofile_export_import() {
	$plugin = new Bp_Xprofile_Export_Import();
	$plugin->run();
}