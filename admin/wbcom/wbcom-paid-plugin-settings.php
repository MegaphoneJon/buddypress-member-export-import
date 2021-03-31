<?php
/**
 * Class to add reviews shortcode.
 *
 * @since    1.0.0
 * @author   Wbcom Designs
 * @package  BuddyPress_Member_Reviews
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Wbcom_Paid_Plugin_Settings' ) ) {

	/**
	 * Class to serve AJAX Calls.
	 *
	 * @author   Wbcom Designs
	 * @since    1.0.0
	 */
	class Wbcom_Paid_Plugin_Settings {

		/**
		 * Admin Menu Pages.
		 */
		public function __construct() {
			add_action( 'admin_menu', array( $this, 'wbcom_admin_license_page' ), 999 );
			add_action( 'wbcom_add_header_menu', array( $this, 'wbcom_add_header_license_menu' ) );
		}

		/**
		 * Admin License Page.
		 */
		public function wbcom_admin_license_page() {
			add_submenu_page(
				'wbcomplugins',
				esc_html__( 'License', 'bp-xprofile-export-import' ),
				esc_html__( 'License', 'bp-xprofile-export-import' ),
				'manage_options',
				'wbcom-license-page',
				array( $this, 'wbcom_license_submenu_page_callback' )
			);
		}

		/**
		 * Function for include license page template.
		 */
		public function wbcom_license_submenu_page_callback() {
			include 'templates/wbcom-license-page.php';
		}

		/**
		 * Function for add license page at header.
		 */
		public function wbcom_add_header_license_menu() {
			$license_page_active = filter_input( INPUT_GET, 'page' ) === 'wbcom-license-page' ? 'is_active' : '';
			?>
			<li class="wb_admin_nav_item <?php echo esc_attr( $license_page_active ); ?>">
				<a href="<?php echo esc_url( get_admin_url() . 'admin.php?page=wbcom-license-page' ); ?>" id="wb_admin_nav_trigger_support">
					<i class="fa fa-wpforms" aria-hidden="true"></i>
					<h4><?php esc_html_e( 'License', 'bp-xprofile-export-import' ); ?></h4>
				</a>
			</li>
			<?php
		}

	}

	/**
	 * Paid Plugin Manager.
	 */
	function instantiate_wbcom_manager() {
		new Wbcom_Paid_Plugin_Settings();
	}

	instantiate_wbcom_manager();
}
