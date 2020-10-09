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
class Bp_Xprofile_Admin_Import_Ajax {

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
	 * Display CSV fields and current xprofile fields
	 *
	 * @since    1.0.0
	 * @access   public
	 * @author   Wbcom Designs
	 */
	public function bpxp_import_csv_header_fields() {
		/**
		* Check ajax nonce security.
		*/
		check_ajax_referer( 'bpxp_ajax_request', 'bpxp_header_nonce' );

		if ( isset( $_POST['action'] ) && 'bpxp_import_header_fields' === $_POST['action'] ) {
			$bpxp_header = array_map( 'sanitize_text_field', wp_unslash( $_POST['bpxp_csv_header'] ) );
			/* Get xprofile fields group and fields name. */
			$bpxp_map_xprofile = BP_XProfile_Group::get( array( 'fetch_fields' => true ) );

			$bpxp_fields_group = array();
			if ( ! empty( $bpxp_map_xprofile ) ) {
				$bpxp_fields_group = array();
				foreach ( $bpxp_map_xprofile as $bpxp_mapfields_key => $bpxp_map_value ) {
					$bpxp_profile_fields                        = array();
					$bpxp_fields_group[ $bpxp_map_value->name ] = $bpxp_map_value->name;
					if ( ! empty( $bpxp_map_value->fields ) ) {
						foreach ( $bpxp_map_value->fields as $bpxp_fields_data ) {
							$bpxp_profile_fields[ $bpxp_fields_data->id ] = $bpxp_fields_data->name;
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
				$current_group .= '<tr><th>' . esc_html__( 'Current xProfile Group Fields', 'bp-xprofile-export-import' ) . '</th>';
				$current_group .= '<th>' . esc_html__( 'Exported xProfile Group Fields', 'bp-xprofile-export-import' ) . '</th></tr>';
				foreach ( $bpxp_fields_group as $bpxp_index => $bpxp_fields ) {
					$current_group .= '<tr class="bpxp-group-heading">';
					$current_group .= '<td colspan="2">' . esc_html__( $bpxp_index, 'bp-xprofile-export-import' ) . '</td></tr>';
					foreach ( $bpxp_fields as $bpxp_key => $bpxp_current_fields ) {
						$temp_name      = strtolower( str_replace( ' ', '_', trim( $bpxp_current_fields ) ) );
						$current_group .= '<tr class="bpxp-group-fields"><td>' . esc_html__( $bpxp_current_fields, 'bp-xprofile-export-import' );
						$current_group .= '</td>';
						if ( ! empty( $bpxp_header ) ) {
							$current_group .= '<td>';
							if ( in_array( $bpxp_current_fields, $bpxp_header ) ) {
								$current_group .= '<input type="hidden" name="' . esc_html__( $bpxp_key ) . '" class="bpxp_current_fields" value="' . esc_html__( $bpxp_current_fields ) . '"/>';
							} else {
								$current_group .= '<input type="hidden" name="' . esc_html__( $bpxp_key ) . '" class="bpxp_current_fields" value=""/>';
							}
							$current_group .= '<select class="bpxp_csv_fields">';
							if ( in_array( $bpxp_current_fields, $bpxp_header ) ) {
								$current_group .= '<option value="' . esc_html__( $bpxp_current_fields ) . '" selected >' . esc_html__( $bpxp_current_fields, 'bp-xprofile-export-import' ) . '</option>';
							} else {
								$current_group .= '<option value="">' . esc_html__( '--- Select CSV Fields---', 'bp-xprofile-export-import' ) . '</option>';
							}
							foreach ( $bpxp_header as $bpxp_header_val ) {
								$current_group .= '<option value="' . esc_html__( $bpxp_header_val ) . '">' . esc_html__( $bpxp_header_val, 'bp-xprofile-export-import' ) . '</option>';
							}
							$current_group .= '<select></td>';
						}
						$current_group .= '</tr>';
					}
				}
				$current_group .= '<br/><tr><td colspan="2"><p class="description"> <b> ' . esc_html__( 'Note:', 'bp-xprofile-export-import' ) . '</b>' . esc_html__( ' Select xProfile Fields from above to insert value for xProfile Fileds. If the fields that exist in the CSV file do not exist in your website, in that case the fields processing will be skipped, otherwise you need to create those fields..', 'bp-xprofile-export-import' ) . '</p></td></tr>';

				$user_meta      = array( 'user_nicename', 'display_name', 'nickname', 'first_name', 'last_name', 'description' );
				$current_group .= '<tr><th> User Meta </th><th> CSV Column </th></tr>';
				foreach ( $user_meta as $meta ) {
					if ( ! empty( $bpxp_header ) ) {
						$current_group .= '<tr><td>' . $meta . '</td><td>';
						if ( in_array( $meta, $bpxp_header ) ) {
							$current_group .= '<input type="hidden" name="' . esc_attr( $meta ) . '" class="bpxp_current_fields" value="' . esc_attr( $meta ) . '"/>';
						} else {
							$current_group .= '<input type="hidden" name="' . esc_attr( $meta ) . '" class="bpxp_current_fields" value=""/>';
						}

						$current_group .= '<select class="bpxp_csv_fields">';
						if ( in_array( $meta, $bpxp_header ) ) {
							$current_group .= '<option value="' . esc_attr( $meta ) . '" selected >' . esc_attr( $meta, 'bp-xprofile-export-import' ) . '</option>';
						} else {
							$current_group .= '<option value="">' . esc_html__( '--- Select CSV Fields---', 'bp-xprofile-export-import' ) . '</option>';
						}
						foreach ( $bpxp_header as $bpxp_header_val ) {

							$current_group .= '<option value="' . esc_attr( $bpxp_header_val ) . '">' . esc_html__( $bpxp_header_val, 'bp-xprofile-export-import' ) . '</option>';
						}
						$current_group .= '<select></td></tr>';
					}
				}
				$current_group .= '</table></div>';
			}
			echo $current_group;
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
		check_ajax_referer( 'bpxp_ajax_request', 'bpxp_csv_nonce' );
		if ( isset( $_POST['action'] ) && 'bpxp_import_csv_data' === $_POST['action'] ) {
			set_time_limit( 0 );
			$member_grp_msg                 = array();
			$bpxp_all_group                 = array();
			$flage                          = false;
			$length                         = 12;
			$include_standard_special_chars = false;
			$bpxp_update_user               = sanitize_text_field( wp_unslash( $_POST['bpxpj_update_user'] ) );
			$pass_encrypte                  = sanitize_text_field( wp_unslash( $_POST['pass_encrypte'] ) );
			$bpxp_members_data              = '';
			if ( ! empty( $_POST['bpxp_csv_file'] ) ) {
				$bpxp_members_data = wp_unslash( $_POST['bpxp_csv_file'] );
				if ( count( $bpxp_members_data[0] ) == 1 ) {
					unset( $bpxp_members_data[0] );
				}
			}

			$bpxp_data_value = array();
			$bpxp_data_key   = array();
			$bpxp_counter    = 0;
			if ( ! empty( $bpxp_members_data ) ) {
				if ( 0 == $_POST['bpxpj_counter'] ) {
					foreach ( $bpxp_members_data as $bpxp_member ) {
						if ( count( $bpxp_member ) > 1 ) {
							foreach ( $bpxp_member as $data ) {
								if ( 0 === $bpxp_counter ) {
									$bpxp_data_key[] = sanitize_text_field( $data );
								} else {
									$bpxp_data_value[ $bpxp_counter ][] = sanitize_text_field( $data );
								}
							}
							$bpxp_counter++;
						}
					}

					if ( in_array( 'user_email', $bpxp_data_key, true ) && in_array( 'user_login', $bpxp_data_key, true ) ) {
						update_option( 'bpxp_csv_headers', $bpxp_data_key );
					} else {
						echo '<div class="bpxp-error-data">';
						echo '<p class="bpxp-error-message bpxp-message">';
						esc_html_e( 'Sorry CVS file did not imported. There are some errors in CSV column name please correct them and try again. Some columns in CSV are required, eg. user_login , user_pass, user_email, user_role.', 'bp-xprofile-export-import' );
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

			/** Import member data and create users. */
			if ( ! empty( $bpxp_users_data ) ) {

				$bpxp_import_error_message   = array();
				$bpxp_import_update_message  = array();
				$bpxp_import_success_message = array();
				$bpxp_grp_msg                = array();
				$bpxp_pass                   = array();
				foreach ( $bpxp_users_data as $bpxp_user ) {
					$flage = false;
					if ( ! empty( $bpxp_user ) ) {
						$bpxp_user_arr  = array();
						$bpxp_user_pass = '';

						foreach ( $bpxp_user as $fields_key => $fields_value ) {
							/** Check if user already exists. */
							if ( 'user_login' == $fields_key && ! empty( $fields_value ) ) {
								$user_id   = username_exists( $fields_value );
								$user_name = $fields_value;
							}
							/* Create user if not exists */
							if ( 'user_email' == $fields_key && ! empty( $fields_value ) ) {
								$bpxp_user_id = '';
								if ( empty( $user_id ) && email_exists( $fields_value ) === false ) {
									$bpxp_password = wp_generate_password( $length, $include_standard_special_chars );

									$user_email                     = $fields_value;
									$bpxp_user_id                   = wp_create_user( $user_name, $bpxp_password, $user_email );
									$bpxp_user_arr[ $bpxp_user_id ] = $bpxp_user_id;
									$bpxp_import_success_message[]  = $fields_value;
								} else {
									/* update existing user */

									if ( 'update-users' == $bpxp_update_user ) {
										$bpxp_ext_user = get_user_by( 'email', $fields_value );
										if ( ! empty( $bpxp_ext_user ) ) {
											$bpxp_user_id                   = $bpxp_ext_user->data->ID;
											$bpxp_user_arr[ $bpxp_user_id ] = $bpxp_user_id;
											$bpxp_import_update_message[]   = $fields_value;
										}
									} else {
										$bpxp_import_error_message[] = $fields_value;
									}
								}
								/* store password */
								if ( $bpxp_user_id ) {
									$bpxp_pass[ $bpxp_user_id ] = $bpxp_user['user_pass'];
								}
							}
							/**
							* Update user meta fields
							*/
							if ( ! empty( $bpxp_user_id ) ) {
								/* Get users role form csv data */
								if ( 'user_role' == $fields_key && ! empty( $fields_value ) ) {
									$id = wp_update_user(
										array(
											'ID'   => $bpxp_user_id,
											'role' => $fields_value,
										)
									);
								}

								if ( 'avatar_path' === $fields_key && ! empty( $fields_value ) ) {
									$this->bpxp_upload_member_avatar( $fields_value, $bpxp_user_id );
									update_user_meta( $bpxp_user_id, 'author_avatar', $fields_value );
								}
								/* Create password */
								if ( 'group_slug' == $fields_key && ! empty( $fields_value ) ) {
									$grp_name = '';
									$grp_name = $this->bpxp_add_members_to_group( $fields_value, $bpxp_user_id );

									if ( ! in_array( $grp_name, $bpxp_grp_msg ) && ! empty( $grp_name ) ) {
										$bpxp_grp_msg[] = $grp_name;
									}
								}
							}
						}

						/* update user xprofile fields */
						if ( ! empty( $bpxp_user_arr ) ) {
							$xfields          = array_map( 'sanitize_text_field', wp_unslash( $_POST['bpxpj_field'] ) );
							$bpxp_xprofiel_id = $this->bpxp_update_user_xprofile_fields( $bpxp_user_arr, $xfields, $bpxp_user );
						}

						if ( ! empty( $bpxp_pass ) ) {
							$this->bpxp_update_user_password( $bpxp_pass, $pass_encrypte );
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
	 * Import user data.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @author   Wbcom Designs
	 * @param    string $url contain export member's avatar url path.
	 * @param    int    $id import member id.
	 */
	public function bpxp_upload_member_avatar( $url, $id ) {

		if ( strpos( $url, '/' ) ) {
			$fn       = explode( '/', $url );
			$filename = array_pop( $fn );
		}
		$path_parts = pathinfo( $url );
		if ( ! preg_match( '/png|gif|jpg|jpeg|bmp|PNG|GIF|JPG|JPEG|BMP/', $filename ) ) {
			return;
		}

		$prefix       = explode( '-', $filename );
		$upload_dir   = wp_upload_dir();
		$avt_dir      = $upload_dir['basedir'] . '/avatars';
		$user_dirname = $upload_dir['basedir'] . '/avatars/' . $id;
		if ( ! file_exists( $avt_dir ) ) {
			wp_mkdir_p( $avt_dir );
			if ( ! file_exists( $user_dirname ) ) {
				wp_mkdir_p( $user_dirname );
			}
		} else {
			if ( ! file_exists( $user_dirname ) ) {
				wp_mkdir_p( $user_dirname );
			}
		}

		header( 'Content-Type: image/jpeg' );
		header( 'Content-Type: image/png' );
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_HEADER, false );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.1 Safari/537.11' );
		$res     = curl_exec( $ch );
		$rescode = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
		curl_close( $ch );

		$thumb  = $user_dirname . '/' . $prefix[0] . '-bpthumb.' . $path_parts['extension'];
		$full   = $user_dirname . '/' . $filename;
		$file_1 = fopen( $thumb, 'w+' );
		$file_2 = fopen( $full, 'w+' );
		fputs( $file_1, $res );
		fputs( $file_2, $res );
		fclose( $file_1 );
		fclose( $file_2 );
	}

	/**
	 * Display bpxp_import_grp_admin_notice admin notice related to member group on import.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @author   Wbcom Designs
	 * @param    string $bpxp_notice contains messages.
	 */
	public function bpxp_import_grp_admin_notice( $bpxp_notice ) {
		if ( ! empty( $bpxp_notice ) ) {
			if ( is_array( $bpxp_notice ) ) {
				foreach ( $bpxp_notice as $key => $notice ) {
					$message = 'Profile field group ' . $notice . ' does not exist! ';
					echo '<div class="bpxp-error-data">';
					echo '<p class="bpxp-error-message bpxp-message">';
					echo esc_html__( $message, 'bp-xprofile-export-import' );
					echo '<a href="javascript:void(0)" class="bpxp-close">x</a></p>';
					echo '</div>';
				}
			}
		}
	}

	/**
	 * Display bpxp_import_admin_notice admin notice in import member page.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @author   Wbcom Designs
	 * @param    string $bpxp_notice admin notice error.
	 * @param    string $bpxp_type error type.
	 */
	public function bpxp_import_admin_notice( $bpxp_notice, $bpxp_type ) {
		if ( ! empty( $bpxp_type ) ) {
			$bpxp_msg      = '';
			$container_cls = '';
			$box_cls       = '';
			switch ( $bpxp_type ) {
				case 'user_exists':
					$bpxp_msg      = ' Member already exists! ';
					$container_cls = 'bpxp-error-data';
					$box_cls       = 'bpxp-error-message bpxp-message';
					break;
				case 'user_create':
					$bpxp_msg      = ' Member created successfully! ';
					$container_cls = 'bpxp-success-data';
					$box_cls       = 'bpxp-success-message bpxp-message';
					break;
				case 'user_update':
					$bpxp_msg      = ' Member updated successfully! ';
					$container_cls = 'bpxp-success-data';
					$box_cls       = 'bpxp-success-message bpxp-message';
					break;
				case 'rong_data':
					$bpxp_msg      = ' ';
					$container_cls = 'bpxp-error-data';
					$box_cls       = 'bpxp-error-message bpxp-message';
					break;
				default:
					$bpxp_msg = ' Users import ';
					break;
			}
			if ( ! empty( $bpxp_type ) && ( $bpxp_notice ) ) {
				if ( is_array( $bpxp_notice ) ) {
					$groups = ' ';
					foreach ( $bpxp_notice as $key => $notice ) {
						$message = $notice . ' ' . $bpxp_msg;
						echo '<div class="' . esc_attr( $container_cls ) . '">';
						echo '<p class="' . esc_attr( $box_cls ) . '">';
						echo sprintf( esc_html__( '%s', 'bp-xprofile-export-import' ), $message );
						echo '<a href="javascript:void(0)" class="bpxp-close">x</a></p>';
						echo '</div>';
					}
				}
			}
		}
	}

	/**
	 * Function bpxp_update_user_password Add user's password from CSV.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @author   Wbcom Designs
	 * @param    string $bpxp_pass  update member password.
	 * @param    string $pass_encrypte  contain option value for encrypt password.
	 */
	public function bpxp_update_user_password( $bpxp_pass, $pass_encrypte ) {
		if ( ! empty( $bpxp_pass ) ) {
			global $wpdb;
			$usertbl = $wpdb->prefix . 'users';
			foreach ( $bpxp_pass as $id => $pass ) {
				if ( ! empty( $pass ) ) {
					if ( ! empty( $pass_encrypte ) ) {
						$pass = $this->bpxp_hash_password( $pass );
					}
					$wpdb->update(
						$usertbl,
						array(
							'user_pass'           => $pass,
							'user_activation_key' => '',
						),
						array( 'ID' => $id )
					);
					wp_cache_delete( $id, 'users' );
					$date = date( 'Y-m-d h:i:m' );
					update_user_meta( $id, 'last_activity', $date );
				}
			}
		}
	}

	/**
	 * Function bpxp_update_user_password Add user's password from CSV.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @author   Wbcom Designs
	 * @param    string $password encrypt member password.
	 * @return   string $password retrun ecrypted password.
	 */
	public function bpxp_hash_password( $password ) {
		global $wp_hasher;
		if ( empty( $wp_hasher ) ) {
			require_once ABSPATH . WPINC . '/class-phpass.php';
			$wp_hasher = new PasswordHash( 8, true );
		}
		return $wp_hasher->HashPassword( trim( $password ) );
	}

	/**
	 * Function bpxp_add_members_to_group Add member's in buddypress groups.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @author   Wbcom Designs
	 * @param    string $bpxpcsv_groups group name.
	 * @param    int    $member_id group id.
	 */
	public function bpxp_add_members_to_group( $bpxpcsv_groups, $member_id ) {
		$group_msg = '';
		$date      = date( 'Y-m-d h:i:m' );
		update_user_meta( $member_id, 'last_activity', $date );

		if ( ! empty( $bpxpcsv_groups ) && strpos( $bpxpcsv_groups, ' - ' ) !== false ) {
			$bpxp_group_array = explode( ' - ', $bpxpcsv_groups );
			foreach ( $bpxp_group_array as $grp ) {
				if ( ! empty( $grp ) ) {
					$group_id = BP_Groups_Group::group_exists( $grp );
					if ( ! empty( $group_id ) && ! empty( $member_id ) ) {
						groups_join_group( $group_id, $member_id );
					} else {
						$group_msg = $grp;
						return $group_msg;
					}
				}
			}
		} else {
			if ( ! empty( $bpxpcsv_groups ) ) {
				$grp_slug = strtolower( $bpxpcsv_groups );
				$group_id = BP_Groups_Group::group_exists( $grp_slug );
				if ( ! empty( $group_id ) && ! empty( $member_id ) ) {
					groups_join_group( $group_id, $member_id );
				} else {
					$group_msg = $bpxpcsv_groups;
					return $group_msg;
				}
			}
		}
	}

	/**
	 * Fucntio bpxp_update_user_xprofile_fields Update user xprofile fields.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @author   Wbcom Designs
	 * @param    int    $bpxp_id    member id.
	 * @param    string $bpxpxfields    xprofile fields name.
	 * @param    string $bpxp_exp_feilds    contain csv file xprofile fields name.
	 */
	public function bpxp_update_user_xprofile_fields( $bpxp_id, $bpxpxfields, $bpxp_exp_feilds ) {
		if ( ! empty( $bpxp_id ) && ! empty( $bpxpxfields ) ) {
			foreach ( $bpxp_id as $key => $id ) {
				foreach ( $bpxpxfields as $fieldkey => $fieldval ) {
					$fieldval   = $fieldval;
					$temp_value = '';

					if ( array_key_exists( $fieldval, $bpxp_exp_feilds ) ) {
						/* update user meta usre nice name */
						wp_update_user(
							array(
								'ID'      => $id,
								$fieldkey => $bpxp_exp_feilds[ $fieldval ],
							)
						);
					}

					if ( array_key_exists( $fieldval, $bpxp_exp_feilds ) ) {
						$temp_value = $bpxp_exp_feilds[ $fieldval ];

						$field = new BP_XProfile_Field( $fieldkey );
						/* check if date type value */
						if ( 'datebox' == $field->type ) {
							$temp_value = date( 'Y-m-d', strtotime( $temp_value ) ) . ' 00:00:00';
						}

						/* check if multi select or checkbox value */
						if ( strpos( $temp_value, '-' ) !== false && 'datebox' != $field->type ) {
							$temp_value = explode( ' - ', $temp_value );
						}
						xprofile_set_field_data( $fieldkey, $id, $temp_value );
					}
				}
			}
		}
	}
}
