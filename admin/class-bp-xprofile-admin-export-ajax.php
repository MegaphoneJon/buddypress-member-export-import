<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       www.wbcomdesigns.com
 * @since      1.0.0
 *
 * @package    Bp_Xprofile_Export_Import
 * @subpackage Bp_Xprofile_Export_Import/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Bp_Xprofile_Export_Import
 * @subpackage Bp_Xprofile_Export_Import/admin
 * @author     Wbcom Designs <admin@gmail.com>
 */
class Bp_Xprofile_Admin_Export_Ajax {
	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

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
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 * @access   public
	 * @author   Wbcom Designs
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Set Buddypress member fields.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @author   Wbcom Designs
	 */
	public function bpxp_get_xprofile_fields() {
		/**
		* Check ajax nonce security.
		*/
		check_ajax_referer( 'bpxp_ajax_request', 'bpxp_fields_nonce' );

		if ( isset( $_POST['action'] ) && 'bpxp_get_export_xprofile_fields' === $_POST['action'] ) {
			$bpxp_field_group_id = empty( array_map( 'sanitize_text_field', wp_unslash( $_POST['bpxp_field_group_id'] ) ) );

			if ( ! empty( $bpxp_field_group_id ) ) {
				$fields = '';
				if ( in_array( 'all-fields-group', $bpxp_field_group_id, true ) ) {
					$bpxp_all_xprofile_fields = BP_XProfile_Group::get( array( 'fetch_fields' => true ) );
					if ( ! empty( $bpxp_all_xprofile_fields ) ) {
						$fields .= '<label for="all-fields-group"><input type="checkbox" name="bpxp_xprofile_fields[]" value="all-xprofile-fields" class="bpxp-all-selected bpxp-all-profile"/>' . esc_html__( 'All X-Profile Fields', 'bp-xprofile-export-import' ) . '</label>';
						foreach ( $bpxp_all_xprofile_fields as $bpxp_fields_key => $bpxp_fields_value ) {
							if ( ! empty( $bpxp_fields_value->fields ) ) {
								foreach ( $bpxp_fields_value->fields as $bpxp_fields_data ) {
									$fields .= '<label for="' . esc_attr( $bpxp_fields_data->name ) . '"><input type="checkbox" name="bpxp_xprofile_fields[]" class="bpxp-single-profile" value="' . esc_attr( $bpxp_fields_data->name ) . '"/>' . esc_html__( $bpxp_fields_data->name, 'bp-xprofile-export-import' ) . '</label>';
								}
							}
						}
					}
				} else {
					$bpxp_gid = array();
					if ( ! empty( $bpxp_field_group_id ) ) {
						foreach ( $bpxp_field_group_id as $bpxp_fgroup_id ) {
							$bpxp_gid[ $bpxp_fgroup_id ] = $bpxp_fgroup_id;
						}
						ksort( $bpxp_gid );
					}
					$bpxp_get_xprofile_fields = BP_XProfile_Group::get( array( 'fetch_fields' => true ) );
					if ( ! empty( $bpxp_get_xprofile_fields ) ) {
						$fields .= '<label for="all-fields-group"><input type="checkbox" name="bpxp_xprofile_fields[]" value="all-xprofile-fields" class="bpxp-all-selected bpxp-all-profile"/>' . esc_html__( 'All X-Profile Fields', 'bp-xprofile-export-import' ) . '</label>';
						foreach ( $bpxp_get_xprofile_fields as $bpxp_fields_key => $bpxp_fields_value ) {
							if ( ! empty( $bpxp_fields_value->fields ) && in_array( $bpxp_fields_value->id, $bpxp_gid ) ) {
								foreach ( $bpxp_fields_value->fields as $bpxp_fields_data ) {
									$fields .= '<label for="' . esc_attr( $bpxp_fields_data->name ) . '"><input type="checkbox" name="bpxp_xprofile_fields[]" class="bpxp-single-profile" value="' . esc_attr( $bpxp_fields_data->name ) . '"/>' . esc_html__( $bpxp_fields_data->name, 'bp-xprofile-export-import' ) . '</label>';
								}
							}
						}
					}
				}
			}
			echo wp_kses_post( $fields );
			die;
		}
	}

	/**
	 * Create CSV file of xprofile fields data.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @author   Wbcom Designs
	 */
	public function bpxp_export_member_data() {
		/**
		* Check ajax nonce security.
		*/
		check_ajax_referer( 'bpxp_ajax_request', 'bpxp_members_nonce' );
		if ( isset( $_POST['action'] ) && 'bpxp_export_xprofile_data' === $_POST['action'] ) {
			$bpxp_bpmember_id      = empty( array_map( 'sanitize_text_field', wp_unslash( $_POST['bpxpj_bpmember'] ) ) );
			$bpxp_field_group_id   = empty( array_map( 'sanitize_text_field', wp_unslash( $_POST['bpxpj_field_group'] ) ) );
			$bpxp_xpro_fields_name = empty( array_map( 'sanitize_text_field', wp_unslash( $_POST['bpxpj_xprofile_fields'] ) ) );
			$bpxp_bpmember_id      = $this->bpxp_remove_array_value( $bpxp_bpmember_id, 'user_id' );
			$bpxp_field_group_id   = $this->bpxp_remove_array_value( $bpxp_field_group_id, 'group_id' );
			$bpxp_xpro_fields_name = $this->bpxp_remove_array_value( $bpxp_xpro_fields_name, 'fields_name' );
			$bpxp_exprot_data      = array();
			$bpxp_user_group       = array();
			if ( ! empty( $bpxp_bpmember_id ) ) {
				foreach ( $bpxp_bpmember_id as $bpxp_id ) {
					$bpxp_member_data  = array();
					$bpxp_members_data = get_userdata( $bpxp_id );
					if ( ! empty( $bpxp_members_data ) ) {
						foreach ( $bpxp_members_data as $members ) {
							$bpxp_member_data['Members']         = $bpxp_members_data->data->ID;
							$bpxp_member_data['user_login']      = $bpxp_members_data->data->user_login;
							$bpxp_member_data['user_pass']       = $bpxp_members_data->data->user_pass;
							$bpxp_member_data['user_nicename']   = $bpxp_members_data->data->user_nicename;
							$bpxp_member_data['user_email']      = $bpxp_members_data->data->user_email;
							$bpxp_member_data['user_url']        = $bpxp_members_data->data->user_url;
							$bpxp_member_data['user_registered'] = $bpxp_members_data->data->user_registered;
							$bpxp_member_data['display_name']    = $bpxp_members_data->data->display_name;
							$bpxp_member_data['user_role']       = $bpxp_members_data->roles[0];
						}
					}
					$bpxp_member_data['nickname']    = get_user_meta( $bpxp_id, 'nickname', true );
					$bpxp_member_data['first_name']  = get_user_meta( $bpxp_id, 'first_name', true );
					$bpxp_member_data['last_name']   = get_user_meta( $bpxp_id, 'last_name', true );
					$bpxp_member_data['description'] = get_user_meta( $bpxp_id, 'description', true );
					if ( bp_is_active( 'groups' ) ) {
						$bpxp_users_group = BP_Groups_Member::get_group_ids( $bpxp_id );
						if ( ! empty( $bpxp_users_group ) ) {
							$bpxp_groups_data            = $this->bpxp_get_group_data( $bpxp_users_group );
							$bpxp_user_group[ $bpxp_id ] = $bpxp_groups_data;
						}
					}
					$bpxp_exprot_data[ $bpxp_id ] = $bpxp_member_data;
				}
			}

			/**
			* Store X-Profile data according to user and fields.
			*/
			$bpxp_export_fields = array();
			if ( ! empty( $bpxp_bpmember_id ) ) {
				foreach ( $bpxp_bpmember_id as $bpxp_user ) {
					$bpxp_fields_data = array();
					foreach ( $bpxp_xpro_fields_name as $bpxp_field ) {
						$bpxp_value = bp_get_profile_field_data( 'field=' . $bpxp_field . '&user_id=' . $bpxp_user );
						if ( strpos( $bpxp_value, '<a href=' ) === 0 ) {
							$result = '';
							preg_match_all( '/<a[^>]+href=([\'"])(?<href>.+?)\1[^>]*>/i', $bpxp_value, $result );
							if ( ! empty( $result['href'][0] ) && strpos( $result['href'][0], 'www' ) !== false ) {
								$bpxp_value = $result['href'][0];
							} else {
								$bpxp_value = '';
							}
						}

						if ( is_array( $bpxp_value ) ) {
							$bpxp_value = implode( ' - ', $bpxp_value );
						}
						$bpxp_value                      = preg_replace( '/[,]/', '', $bpxp_value );
						$bpxp_fields_data[ $bpxp_field ] = $bpxp_value;
					}
					$bpxp_export_fields[ $bpxp_user ] = $bpxp_fields_data;
				}
			}

			if ( ! empty( $bpxp_exprot_data ) ) {
				ksort( $bpxp_exprot_data );
			}
			if ( ! empty( $bpxp_user_group ) ) {
				ksort( $bpxp_user_group );
			}
			if ( ! empty( $bpxp_export_fields ) ) {
				ksort( $bpxp_export_fields );
			}
			$bpxp_export_users = $this->bpxp_merge_users_data( $bpxp_exprot_data, $bpxp_user_group, $bpxp_export_fields );
		}
		echo wp_json_encode( $bpxp_export_users );
		die;
	}

	/**
	 * Remove extra value from array.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @author   Wbcom Designs
	 * @param    Array $array_data csv data.
	 * @param    Array $array_type csv data type.
	 * @return   Array  Remove extra header fields from CSV.
	 */
	public function bpxp_remove_array_value( $array_data, $array_type ) {
		$bpxp_id_index = '';
		switch ( $array_type ) {
			case 'user_id':
				if ( ! empty( $array_data ) ) {
					$bpxp_id_index = array_search( 'bpxp-all-user', $array_data, true );
					if ( 'bpxp-all-user' === $bpxp_id_index ) {
						unset( $array_data[ $bpxp_id_index ] );
					}
					return $array_data;
				}
				break;
			case 'group_id':
				if ( ! empty( $array_data ) ) {
					$bpxp_id_index = array_search( 'all-fields-group', $array_data, true );
					if ( 'all-fields-group' === $bpxp_id_index ) {
						unset( $array_data[ $bpxp_id_index ] );
					}
					return $array_data;
				}
				break;
			case 'fields_name':
				if ( ! empty( $array_data ) ) {
					$bpxp_id_index = array_search( 'all-xprofile-fields', $array_data, true );
					if ( 'all-xprofile-fields' === $bpxp_id_index ) {
						unset( $array_data[ $bpxp_id_index ] );
					}
					return $array_data;
				}
				break;
			default:
				return $array_data;
		}
	}

	/**
	 * Get user's group data info and return array.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @author   Wbcom Designs
	 * @param    array $group_id group id.
	 * @return   Array $bpxp_members_group Return user group data details.
	 */
	public function bpxp_get_group_data( $group_id ) {
		if ( ! empty( $group_id ) ) {
			$bpxp_members_group = array();
			$temp_name          = array();
			if ( ! empty( $group_id['groups'] ) ) {
				foreach ( $group_id['groups'] as $id ) {
					$bpxp_groups   = groups_get_group( array( 'group_id' => $id ) );
					$group_creater = get_userdata( $bpxp_groups->creator_id );
					$temp_name[]   = $bpxp_groups->slug;
				}
			}
			$bpxp_members_group['group_slug'] = implode( ' - ', $temp_name );
		}
		return $bpxp_members_group;
	}

	/**
	 * Merge user data into single array.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @author   Wbcom Designs
	 * @param    array $first_array    contains members data.
	 * @param    array $second_array   contains xprofile fields data.
	 * @param    array $third_array    contains groups data.
	 * @return   array Return users data for export csv.
	 */
	public function bpxp_merge_users_data( $first_array, $second_array, $third_array ) {
		$bpxp_export_file = array();
		if ( ! empty( $first_array ) && ! empty( $third_array ) && ! empty( $second_array ) ) {
			foreach ( $first_array as $index => $value ) {
				$result             = array_merge( $value, $third_array[ $index ], $second_array[ $index ] );
				$bpxp_export_file[] = $result;
			}
		} elseif ( ! empty( $first_array ) && ! empty( $third_array ) ) {
			foreach ( $first_array as $index => $value ) {
				$result             = array_merge( $value, $third_array[ $index ] );
				$bpxp_export_file[] = $result;
			}
		} else {
				$bpxp_export_file[] = $first_array;
		}
		return $bpxp_export_file;
	}
}
