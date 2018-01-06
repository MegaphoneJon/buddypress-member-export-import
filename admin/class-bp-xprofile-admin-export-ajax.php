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
class Bp_Xprofile_Export_Admin_Ajax {
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
	 * Set Buddypress member fields
	 *
	 * @since    1.0.0
	 * @access   public
	 * @author   Wbcom Designs
	 */
	public function bpxp_get_xprofile_fields() {
		if ( isset( $_POST['action'] ) && $_POST['action'] == 'bpxp_get_export_xprofile_fields' ) {
			$bpxp_field_group_id = array_map( 'sanitize_text_field', wp_unslash( $_POST['bpxp_field_group_id'] ) );
			if ( ! empty( $bpxp_field_group_id ) ) {
				$bpxp_set_fields = '';
				if ( in_array( 'all-fields-group', $bpxp_field_group_id ) ) {
					$bpxp_all_xprofile_fields = BP_XProfile_Group::get( array( 'fetch_fields' => true ) );
					if ( ! empty( $bpxp_all_xprofile_fields ) ) {
						$bpxp_set_fields .= '<label for="all-fields-group"><input type="checkbox" name="bpxp_xprofile_fields[]" value="all-xprofile-fields" class="bpxp-all-selected bpxp-all-profile"/>All X-Profile Fields</label>';
						foreach ( $bpxp_all_xprofile_fields as $bpxp_fieldsKey => $bpxp_fieldsValue ) {
							if ( ! empty( $bpxp_fieldsValue->fields ) ) {
								foreach ( $bpxp_fieldsValue->fields as $bpxp_fieldsData ) {
									$bpxp_set_fields .= '<label for="' . $bpxp_fieldsData->name . '"><input type="checkbox" name="bpxp_xprofile_fields[]" class="bpxp-single-profile" value="' . $bpxp_fieldsData->name . '"/>' . $bpxp_fieldsData->name . '</label>';
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
						$bpxp_set_fields .= '<label for="all-fields-group"><input type="checkbox" name="bpxp_xprofile_fields[]" value="all-xprofile-fields" class="bpxp-all-selected bpxp-all-profile"/>All X-Profile Fields</label>';
						foreach ( $bpxp_get_xprofile_fields as $bpxp_fieldsKey => $bpxp_fieldsValue ) {
							if ( ! empty( $bpxp_fieldsValue->fields ) && in_array( $bpxp_fieldsValue->id, $bpxp_gid ) ) {
								foreach ( $bpxp_fieldsValue->fields as $bpxp_fieldsData ) {
									$bpxp_set_fields .= '<label for="' . $bpxp_fieldsData->name . '"><input type="checkbox" name="bpxp_xprofile_fields[]" class="bpxp-single-profile" value="' . $bpxp_fieldsData->name . '"/>' . $bpxp_fieldsData->name . '</label>';
								}
							}
						}
					}
				}
			}
			_e( $bpxp_set_fields, BPXP_TEXT_DOMAIN );
			die;
		}
	}
	/**
	 * Create CSV file of xprofile fields data
	 *
	 * @since    1.0.0
	 * @access   public
	 * @author   Wbcom Designs
	 */
	public function bpxp_export_member_data() {
		if ( isset( $_POST['action'] ) && $_POST['action'] == 'bpxp_export_xprofile_data' ) {
			$bpxp_bpmemberID      = array_map( 'sanitize_text_field', wp_unslash( $_POST['bpxpj_bpmember'] ) );
			$bpxp_field_groupID   = array_map( 'sanitize_text_field', wp_unslash( $_POST['bpxpj_field_group'] ) );
			$bpxp_xpro_fieldsName = array_map( 'sanitize_text_field', wp_unslash( $_POST['bpxpj_xprofile_fields'] ) );
			$bpxp_bpmemberID      = $this->bpxp_remove_array_value( $bpxp_bpmemberID, 'user_id' );
			$bpxp_field_groupID   = $this->bpxp_remove_array_value( $bpxp_field_groupID, 'group_id' );
			$bpxp_xpro_fieldsName = $this->bpxp_remove_array_value( $bpxp_xpro_fieldsName, 'fields_name' );
			$bpxp_exprot_data     = array();
			$bpxp_user_group      = array();
			if ( ! empty( $bpxp_bpmemberID ) ) {
				foreach ( $bpxp_bpmemberID as $bpxp_ID ) {
					$bpxp_memberData   = array();
					$bpxp_members_data = get_userdata( $bpxp_ID );
					if ( ! empty( $bpxp_members_data ) ) {
						foreach ( $bpxp_members_data as $members ) {
							$bpxp_memberData['Members']         = $bpxp_members_data->data->ID;
							$bpxp_memberData['user_login']      = $bpxp_members_data->data->user_login;
							$bpxp_memberData['user_pass']       = $bpxp_members_data->data->user_pass;
							$bpxp_memberData['user_nicename']   = $bpxp_members_data->data->user_nicename;
							$bpxp_memberData['user_email']      = $bpxp_members_data->data->user_email;
							$bpxp_memberData['user_url']        = $bpxp_members_data->data->user_url;
							$bpxp_memberData['user_registered'] = $bpxp_members_data->data->user_registered;
							// $bpxp_memberData['user_status']   = $bpxp_members_data->data->user_status;
							$bpxp_memberData['display_name'] = $bpxp_members_data->data->display_name;
							$bpxp_memberData['user_role']    = $bpxp_members_data->roles[0];
						}
					}
					$bpxp_memberData['avatar_path'] = get_avatar_url( $bpxp_ID );
					if ( bp_is_active( 'groups' ) ) {
						$bpxp_usersGroup = BP_Groups_Member::get_group_ids( $bpxp_ID );
						if ( ! empty( $bpxp_usersGroup ) ) {
							$bpxp_groups_data            = $this->bpxp_get_group_data( $bpxp_usersGroup );
							$bpxp_user_group[ $bpxp_ID ] = $bpxp_groups_data;
						}
					}
					$bpxp_exprot_data[ $bpxp_ID ] = $bpxp_memberData;
				}
			}
			/**
			* Store X-Profile data according to user and fields
			*/
			$bpxp_exportFields = array();
			foreach ( $bpxp_bpmemberID as $bpxp_user ) {
				$bpxp_fieldsData = array();
				foreach ( $bpxp_xpro_fieldsName as $bpxp_field ) {
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
					$bpxp_value                     = preg_replace( '/[,]/', '', $bpxp_value );
					$bpxp_fieldsData[ $bpxp_field ] = $bpxp_value;
				}
				$bpxp_exportFields[ $bpxp_user ] = $bpxp_fieldsData;
			}

			if ( ! empty( $bpxp_exprot_data ) ) {
				ksort( $bpxp_exprot_data );
			}
			if ( ! empty( $bpxp_user_group ) ) {
				ksort( $bpxp_user_group );
			}
			if ( ! empty( $bpxp_exportFields ) ) {
				ksort( $bpxp_exportFields );
			}
			$bpxp_export_users = $this->bpxp_merge_users_data( $bpxp_exprot_data, $bpxp_user_group, $bpxp_exportFields );
		}
		echo json_encode( $bpxp_export_users );
		die;
	}
	/**
	 * Remove extra value from array
	 *
	 * @since    1.0.0
	 * @access   public
	 * @author   Wbcom Designs
	 * @param    Array $arrayData csv data
	 * @param    Array $arrayType csv data type
	 * @return   Array  Remove extra header fields from CSV
	 */
	public function bpxp_remove_array_value( $arrayData, $arrayType ) {
		$bpxp_id_index = '';
		switch ( $arrayType ) {
			case 'user_id':
				if ( ! empty( $arrayData ) ) {
					$bpxp_id_index = array_search( 'bpxp-all-user', $arrayData );
					if ( $bpxp_id_index == 'bpxp-all-user' ) {
						unset( $arrayData[ $bpxp_id_index ] );
					}
					return $arrayData;
				}
				break;
			case 'group_id':
				if ( ! empty( $arrayData ) ) {
					$bpxp_id_index = array_search( 'all-fields-group', $arrayData );
					if ( $bpxp_id_index == 'all-fields-group' ) {
						unset( $arrayData[ $bpxp_id_index ] );
					}
					return $arrayData;
				}
				break;
			case 'fields_name':
				if ( ! empty( $arrayData ) ) {
					$bpxp_id_index = array_search( 'all-xprofile-fields', $arrayData );
					if ( $bpxp_id_index == 'all-xprofile-fields' ) {
						unset( $arrayData[ $bpxp_id_index ] );
					}
					return $arrayData;
				}
				break;
			default:
				return $arrayData;
		}
	}
	/**
	 * Get user's group data info and return array
	 *
	 * @since    1.0.0
	 * @access   public
	 * @author   Wbcom Designs
	 * @param    array $groupID group id
	 * @return   Array  $bpxp_members_group Return user group data details
	 */
	public function bpxp_get_group_data( $groupID ) {
		if ( ! empty( $groupID ) ) {
			$bpxp_members_group = array();
			$tempName           = array();
			if ( ! empty( $groupID['groups'] ) ) {
				foreach ( $groupID['groups'] as $id ) {
					$bpxp_groups  = groups_get_group( array( 'group_id' => $id ) );
					$groupCreater = get_userdata( $bpxp_groups->creator_id );
					$tempName[]   = $bpxp_groups->name;
				}
			}
			$bpxp_members_group['group_name'] = implode( ' - ', $tempName );
		}
		return $bpxp_members_group;
	}
	/**
	 * Merge user data into single array.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @author   Wbcom Designs
	 * @return   Array    Return users data for export csv
	 */
	public function bpxp_merge_users_data( $firstArray, $secondArray, $thirdArray ) {
		$bpxp_export_File = array();
		if ( ! empty( $firstArray ) && ! empty( $thirdArray ) && ! empty( $secondArray ) ) {
			foreach ( $firstArray as $index => $value ) {
				$result             = array_merge( $value, $thirdArray[ $index ], $secondArray[ $index ] );
				$bpxp_export_File[] = $result;
			}
		} elseif ( ! empty( $firstArray ) && ! empty( $thirdArray ) ) {
			foreach ( $firstArray as $index => $value ) {
				$result             = array_merge( $value, $thirdArray[ $index ] );
				$bpxp_export_File[] = $result;
			}
		} else {
				$bpxp_export_File[] = $firstArray;
		}
		return $bpxp_export_File;
	}
}
