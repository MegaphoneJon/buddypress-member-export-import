<?php
/**
 * The plugin bootstrap file.
 *
 * This file is read by WordPress to generate the plugin information in the plugin.
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              www.wbcomdesigns.com
 * @since             1.0.0
 * @package           Bp_Xprofile_Export_Import
 *
 * @wordpress-plugin
 * Plugin Name:       BuddyPress Member Export Import
 * Plugin URI:        https://wbcomdesigns.com/contact/
 * Description:       Buddypress Member Export Import plugin bring you feature to export Buddypress members and x-profile fields data into CSV file and import buddypress members from CSV file.

 *
 * Version:           1.0.1
 * Author:            Wbcom Designs
 * Author URI:        www.wbcomdesigns.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bp-xprofile-export-import
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
* Constants used in the plugin.
*/
if ( ! defined( 'BPXP_PLUGIN_VERSION' ) ) {
	define( 'BPXP_PLUGIN_VERSION', '1.0.1' );
}
if ( ! defined( 'BPXP_PLUGIN_FILE' ) ) {
	define( 'BPXP_PLUGIN_FILE', __FILE__ );
}
if ( ! defined( 'BPXP_PLUGIN_PATH' ) ) {
	define( 'BPXP_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'BPXP_PLUGIN_URL' ) ) {
	define( 'BPXP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}


if ( ! function_exists( 'bpxp_admin_page_link' ) ) {

	/**
	 * Add setting link.
	 *
	 * @author   Wbcom Designs
	 * @since    1.0.0
	 * @param    string $links contain plugin setting link.
	 */
	function bpxp_admin_page_link( $links ) {
		$bpxp_links = array(
			'<a href="' . admin_url( 'admin.php?page=bpxp-member-export-import' ) . '">' . __( 'Settings', 'bp-xprofile-export-import' ) . '</a>',
			'<a href="https://wbcomdesigns.com/contact/" target="_blank">' . __( 'Support', 'bp-xprofile-export-import' ) . '</a>',
		);
		return array_merge( $links, $bpxp_links );
	}
}

if ( ! function_exists( 'bpxp_plugins_files' ) ) {

	add_action( 'plugins_loaded', 'bpxp_plugins_files' );

	/**
	 * Include requir files
	 *
	 * @author   Wbcom Designs
	 * @since    1.0.0
	 */
	function bpxp_plugins_files() {
		if ( ! in_array( 'buddypress/bp-loader.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) {
			add_action( 'admin_notices', 'bpxp_admin_notice' );
		} else {
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'bpxp_admin_page_link' );
			bpxp_run_bp_xprofile_export_import();
		}
	}
}

if ( ! function_exists( 'bpxp_admin_notice' ) ) {
	/**
	 * Display admin notice
	 *
	 * @author   Wbcom Designs
	 * @since    1.0.0
	 */
	function bpxp_admin_notice() {
		?>
		<div class="error notice is-dismissible">
			<p><?php esc_html_e( 'The Buddypress Member Export Import plugin requires Buddypress plugin to be installed and active', 'bp-xprofile-export-import' ); ?></p>
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
