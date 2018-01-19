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
class Bp_Xprofile_Import_Admin_Ajax {

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
	 * Display CSV fields and current xprofile fields.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @author   Wbcom Designs
	 */
	public function bpxp_import_csv_header_fields() {
		if ( isset( $_POST['action'] ) && $_POST['action'] == 'bpxp_import_header_fields' ) {
			$bpxp_header = array_map( 'sanitize_text_field', wp_unslash( $_POST['bpxp_csv_header'] ) );
			/** Get xprofile fields group and fields name. */
			$bpxp_map_xprofile = BP_XProfile_Group::get( array( 'fetch_fields' => true ) );

			$bpxp_fields_group = array();
			if ( ! empty( $bpxp_map_xprofile ) ) {
				$bpxp_fields_group = array();
				foreach ( $bpxp_map_xprofile as $bpxp_mapfieldsKey => $bpxp_map_value ) {
					$bpxp_profile_fields                       = array();
					$bpxp_fields_group[ $bpxp_map_value->name ] = $bpxp_map_value->name;
					if ( ! empty( $bpxp_map_value->fields ) ) {
						foreach ( $bpxp_map_value->fields as $bpxp_fieldsData ) {
							$bpxp_profile_fields[ $bpxp_fieldsData->id ] = $bpxp_fieldsData->name;
						}
						$bpxp_fields_group[ $bpxp_map_value->name ] = $bpxp_profile_fields;
					}
				}
			}

			/** Create HTML for current group fields. */
			if ( ! empty( $bpxp_fields_group ) ) {

				/**
				* Start Group fields and csv x-profile fields maping.
				* Create HTML and insert after file element.
				* in Member impor page.
				*/
				$current_group  = '';
				$current_group .= '<div class="bpxp-admin-row bpxp-maping">';
				$current_group .= '<table class="bpxp-admin-table" id="bpxp-fields-maping">';
				$current_group .= '<tr><th>Current xProfile Group Fields</th>';
				$current_group .= '<th>Exported xProfile Group Fields</th></tr>';
				foreach ( $bpxp_fields_group as $bpxp_index => $bpxp_fields ) {
					$current_group .= '<tr class="bpxp-group-heading">';
					$current_group .= '<td colspan="2">' . $bpxp_index . '</td></tr>';
					foreach ( $bpxp_fields as $bpxp_key => $bpxp_current_fields ) {
						$tempName      = strtolower( str_replace( ' ', '_', trim( $bpxp_current_fields ) ) );
						$current_group .= '<tr class="bpxp-group-fields"><td>' . $bpxp_current_fields;
						$current_group .= '</td>';
						if ( ! empty( $bpxp_header ) ) {
							$current_group .= '<td>';
							$current_group .= '<input type="hidden" name="' . $bpxp_key . '" class="bpxp_current_fields" value=""/>';
							$current_group .= '<select class="bpxp_csv_fields">';
							$current_group .= '<option value="">--- Select CSV Fields---</option>';
							foreach ( $bpxp_header as $bpxp_header_val ) {
								$current_group .= '<option value="' . $bpxp_header_val . '">' . $bpxp_header_val . '</option>';
							}
							$current_group .= '<select></td>';
						}
						$current_group .= '</tr>';
					}
				}
				$current_group .= '<br/><tr><td colspan="2"><p class="description"> <b>Note:</b> Select xProfile Fields from above to insert value for xProfile Fileds. If the fields that exist in the CSV file do not exist in your website, in that case the fields processing will be skipped, otherwise you need to create those fields..</p></td></tr>';
				$current_group .= '</table></div>';
			}
			echo sprintf( __( "%s", 'bp-xprofile-export-import'), $current_group );
			die;
		}
	}

	/**
	 * Import user data.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @author   Wbcom Designs
	 */
	public function bpxp_import_csv_member_data() {

		/**
		* This function is import csv data into database.
		*/
		if ( isset( $_POST['action'] ) && $_POST['action'] == 'bpxp_import_csv_data' ) {
			set_time_limit( 0 );
			$member_grp_msg   = array();
			$flage            = false;
			$bpxp_update_user = sanitize_text_field( $_POST['bpxpj_update_user'] );

			$bpxp_members_data = '';
			$bpxp_members_data = '';
			if ( ! empty( $_POST['bpxp_csv_file'] ) ) {
				$bpxp_members_data = $_POST['bpxp_csv_file'];
				if ( count( $bpxp_members_data[0] ) == 1 ) {
					unset( $bpxp_members_data[0] );
				}
			}

			$bpxp_data_value = array();
			$bpxp_data_key   = array();
			$bpxp_counter    = 0;
			if ( ! empty( $bpxp_members_data ) ) {
				if ( $_POST['bpxpj_counter'] == 0 ) {
					foreach ( $bpxp_members_data as $bpxp_member ) {
						if ( count( $bpxp_member ) > 1 ) {
							foreach ( $bpxp_member as $data ) {
								if ( $bpxp_counter == 0 ) {
									$bpxp_data_key[] = sanitize_text_field( $data );
								} else {
									$bpxp_data_value[ $bpxp_counter ][] = sanitize_text_field( $data );
								}
							}
							$bpxp_counter++;
						}
					}

					if ( in_array( 'user_email', $bpxp_data_key ) && in_array( 'user_login', $bpxp_data_key ) ) {
						update_option( 'bpxp_csv_headers', $bpxp_data_key );
					} else {
						echo '<div class="bpxp-error-data">';
						echo '<p class="bpxp-error-message bpxp-message">';
						_e( 'Sorry CVS file did not imported. There are some errors in CSV column name please correct them and try again. Some columns in CSV are required, eg. user_login , user_pass, user_email, user_role.', 'bp-xprofile-export-import' );
						echo '<a href="javascript:void(0)" class="bpxp-close">x</a></p>';
						echo '</div>';
						exit;
					}
				} else {
					foreach ( $bpxp_members_data as $bpxp_member ) {
						foreach ( $bpxp_member as $data ) {
								$bpxp_data_value[ $bpxp_counter ][] = sanitize_text_field( $data );
						}
						$bpxp_counter++;
					}
					$bpxp_data_key = get_option( 'bpxp_csv_headers', true );
				}
			}

			/* Combine csv header as key and row data as value */
			$bpxp_users_data = array();
			if ( ! empty( $bpxp_data_value ) ) {
				foreach ( $bpxp_data_value as $bpxp_array ) {
					$bpxp_users_data[] = array_combine( $bpxp_data_key, $bpxp_array );
				}
			}

			/* Import member data and create users */
			if ( ! empty( $bpxp_users_data ) ) {

				$bpxp_import_error_message   	= array();
				$bpxp_import_update_message  	= array();
				$bpxp_import_success_message 	= array();
				$bpxp_grp_msg                	= array();
				$bpxp_pass                   	= array();
				foreach ( $bpxp_users_data as $bpxp_user ) {
					$flage = false;
					if ( ! empty( $bpxp_user ) ) {
						$bpxp_user_array  		= array();
						$bpxp_userPass 			= '';

						foreach ( $bpxp_user as $fields_key => $fields_value ) {
							/* Check if user already exists */
							if ( $fields_key == 'user_login' && ! empty( $fields_value ) ) {
								$user_id   		= username_exists( $fields_value );
								$user_name 		= $fields_value;
							}
							/* Create user if not exists */
							if ( $fields_key == 'user_email' && ! empty( $fields_value ) ) {
								$bpxp_user_id 	= '';
								if ( empty( $user_id ) && email_exists( $fields_value ) == false ) {
									$bpxp_password = wp_generate_password( $length = 12, $include_standard_special_chars = false );

									$user_email 			= $fields_value;
									$bpxp_user_id           = wp_create_user( $user_name, $bpxp_password, $user_email );
									$bpxp_user_array[ $bpxp_user_id ]  	= $bpxp_user_id;
									$bpxp_import_success_message[] 		= $fields_value;
								} else {
									/** update existing user. */

									if ( $bpxp_update_user == 'update-users' ) {
										$bpxp_ext_user = get_user_by( 'email', $fields_value );
										if ( ! empty( $bpxp_ext_user ) ) {
											$bpxp_user_id                  = $bpxp_ext_user->data->ID;
											$bpxp_user_array[ $bpxp_user_id ] = $bpxp_user_id;
											$bpxp_import_update_message[] = $fields_value;
										}
									} else {
										$bpxp_import_error_message[] = $fields_value;
									}
								}
								/** store password. */
								if ( $bpxp_user_id ) {
									$bpxp_pass[ $bpxp_user_id ] = $bpxp_user['user_pass'];
								}
							}

							/**
							* Update user meta fields.
							*/
							if ( ! empty( $bpxp_user_id ) ) {
								/** Get users role form csv data. */
								if ( $fields_key == 'user_role' && ! empty( $fields_value ) ) {
									$id = wp_update_user(
										array(
											'ID'   => $bpxp_user_id,
											'role' => $fields_value,
										)
									);
								}

								if ( $fields_key == 'avatar_path' && ! empty( $fields_value ) ) {
									update_user_meta( $bpxp_user_id, 'author_avatar', $fields_value );
								}

								/** update user meta usre nice name. */
								if ( $fields_key == 'user_nicename' && ! empty( $fields_value ) ) {
									wp_update_user(
										array(
											'ID' => $bpxp_user_id,
											'user_nicename',
											$fields_value,
										)
									);
								}
								/** update user meta display name. */
								if ( $fields_key == 'display_name' && ! empty( $fields_value ) ) {
									wp_update_user(
										array(
											'ID' => $bpxp_user_id,
											'display_name',
											$fields_value,
										)
									);
								}
								/** Create password. */
								if ( $fields_key == 'group_name' && ! empty( $fields_value ) ) {
									$grp_name = '';
									$grp_name = $this->bpxp_add_members_to_group( $fields_value, $bpxp_user_id );

									if ( ! in_array( $grp_name, $bpxp_grp_msg ) && ! empty( $grp_name ) ) {
										$bpxp_grp_msg[] = $grp_name;
									}
								}
							}
						}

						/** update user xprofile fields. */
						if ( ! empty( $bpxp_user_array ) ) {
							$bpxp_xprofiel_id = $this->bpxp_update_user_xprofile_fields( $bpxp_user_array, $_POST['bpxpj_field'], $bpxp_user );
						}

						if ( ! empty( $bpxp_pass ) ) {
							$this->bpxp_update_user_password( $bpxp_pass );
						}
					}
				}
			}

			if ( ! empty( $bpxp_grp_msg ) ) {
				$this->bpxp_import_grp_admin_notice( $bpxp_grp_msg );
			}
			$this->bpxp_import_admin_notice( $bpxp_import_update_message, 'user_update' );
			$this->bpxp_import_admin_notice( $bpxp_import_error_message, 'user_exists' );
			$this->bpxp_import_admin_notice( $bpxp_import_success_message, 'user_create' );
		}
		die;
	}

	/**
	 * Display admin notice related to member group on import.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @author   Wbcom Designs
	 * @param    $bpxp_notice
	 */
	public function bpxp_import_grp_admin_notice( $bpxp_notice ) {
		if ( ! empty( $bpxp_notice ) ) {
			if ( is_array( $bpxp_notice ) ) {
				foreach ( $bpxp_notice as $key => $notice ) {
					echo '<div class="bpxp-error-data">';
					echo '<p class="bpxp-error-message bpxp-message">';
					echo sprintf( __( "Profile field group %s does not exist!", 'bp-xprofile-export-import'), $notice );
					echo '<a href="javascript:void(0)" class="bpxp-close">x</a></p>';
					echo '</div>';
				}
			}
		}
	}
	/**
	 * Display admin notice in import member page.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @author   Wbcom Designs
	 * @param    $bpxp_notice admin notice error
	 * @param    $bpxp_type error type
	 */
	public function bpxp_import_admin_notice( $bpxp_notice, $bpxp_type ) {
		if ( ! empty( $bpxp_type ) ) {
			$bpxp_msg      = '';
			$container_cls = '';
			$boxCls       = '';
			switch ( $bpxp_type ) {
				case 'user_exists':
					$bpxp_msg      = ' Member already exists! ';
					$container_cls = 'bpxp-error-data';
					$boxCls       = 'bpxp-error-message bpxp-message';
					break;
				case 'user_create':
					$bpxp_msg      = ' Member created successfully! ';
					$container_cls = 'bpxp-success-data';
					$boxCls       = 'bpxp-success-message bpxp-message';
					break;
				case 'user_update':
					$bpxp_msg      = ' Member updated successfully! ';
					$container_cls = 'bpxp-success-data';
					$boxCls       = 'bpxp-success-message bpxp-message';
					break;
				case 'rong_data':
					$bpxp_msg      = ' ';
					$container_cls = 'bpxp-error-data';
					$boxCls       = 'bpxp-error-message bpxp-message';
					break;
				default:
					$bpxp_msg = ' Users import ';
					break;
			}
			if ( ! empty( $bpxp_type ) && ( $bpxp_notice ) ) {
				if ( is_array( $bpxp_notice ) ) {
					$groups = ' ';
					foreach ( $bpxp_notice as $key => $notice ) {
						echo '<div class="' . $container_cls . '">';
						echo '<p class="' . $boxCls . '">';
						echo sprintf( __( "%1$s  %1$s ", 'bp-xprofile-export-import'), $notice , $bpxp_msg);
						echo '<a href="javascript:void(0)" class="bpxp-close">x</a></p>';
						echo '</div>';
					}
				}
			}
		}
	}

	/**
	 * Add user's password from CSV.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @author   Wbcom Designs
	 * @param    $bpxp_pass  update member password.
	 */
	public function bpxp_update_user_password( $bpxp_pass ) {
		if ( ! empty( $bpxp_pass ) ) {
			global $wpdb;
			$usertbl = $wpdb->prefix . 'users';
			foreach ( $bpxp_pass as $id => $pass ) {
				$wpdb->update(
					$usertbl, array(
						'user_pass'           => $pass,
						'user_activation_key' => '',
					), array( 'ID' => $id )
				);
				wp_cache_delete( $id, 'users' );
				$date = date( 'Y-m-d h:i:m' );
				update_user_meta( $id, 'last_activity', $date );
			}
		}
	}

	/**
	 * Add member's in buddypress groups.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @author   Wbcom Designs
	 * @param    $bpxpcsv_groups group name.
	 * @param    $member_id group id.
	 */
	public function bpxp_add_members_to_group( $bpxpcsv_groups, $member_id ) {
		$group_msg = '';
		$date     = date( 'Y-m-d h:i:m' );
		update_user_meta( $member_id, 'last_activity', $date );

		if ( ! empty( $bpxpcsv_groups ) && strpos( $bpxpcsv_groups, ' - ' ) !== false ) {
			$bpxp_groups = explode( ' - ', $bpxpcsv_groups );
			foreach ( $bpxp_groups as $grp ) {
				if ( ! empty( $grp ) ) {
					$group_slug = strtolower( $grp );
					$grp_id   = BP_Groups_Group::group_exists( $group_slug );
					if ( ! empty( $grp_id ) && ! empty( $member_id ) ) {
						groups_join_group( $grp_id, $member_id );
					} else {
						$group_msg = $grp;
						return $group_msg;
					}
				}
			}
		} else {
			if ( ! empty( $bpxpcsv_groups ) ) {
				$group_slug = strtolower( $bpxpcsv_groups );
				$grp_id   = BP_Groups_Group::group_exists( $group_slug );
				if ( ! empty( $grp_id ) && ! empty( $member_id ) ) {
					groups_join_group( $grp_id, $member_id );
				} else {
					$group_msg = $bpxpcsv_groups;
					return $group_msg;
				}
			}
		}
	}

	/**
	 * Update user xprofile fields.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @author   Wbcom Designs
	 */
	public function bpxp_update_user_xprofile_fields( $bpxp_id, $bpxpxfields, $bpxp_exp_feilds ) {
		if ( ! empty( $bpxp_id ) && ! empty( $bpxpxfields ) ) {
			foreach ( $bpxp_id as $key => $id ) {
				foreach ( $bpxpxfields as $fieldkey => $fieldval ) {
					$fieldval = sanitize_text_field( $fieldval );
					$temp_val  = '';

					if ( array_key_exists( $fieldval, $bpxp_exp_feilds ) ) {
						$temp_val = $bpxp_exp_feilds[ $fieldval ];

						$field = new BP_XProfile_Field( $fieldkey );

						/* check if date type value */
						if ( $field->type == 'datebox' ) {
							$temp_val = date( 'Y-m-d', strtotime( $temp_val ) ) . ' 00:00:00';
						}

						/* check if multi select or checkbox value */
						if ( strpos( $temp_val, '-' ) !== false && $field->type != 'datebox' ) {
							$temp_val = explode( ' - ', $temp_val );
						}
						xprofile_set_field_data( $fieldkey, $id, $temp_val );
					}
				}
			}
		}
	}
}