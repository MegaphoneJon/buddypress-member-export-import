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
	* @param      string    $plugin_name       The name of this plugin.
	* @param      string    $version    The version of this plugin.
	* @access   public
	* @author   Wbcom Designs
	*/
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	* Display CSV fields and current xprofile fields
	*
	* @since    1.0.0
	* @access   public
	* @author   Wbcom Designs
	*/
	public function bpxp_import_csv_header_fields(){
		if(isset($_POST['action']) && $_POST['action'] == 'bpxp_import_header_fields'){
			$bpxp_header 	= array_map( 'sanitize_text_field', wp_unslash( $_POST['bpxp_csv_header']) );
			/* Get xprofile fields group and fields name */
			$bpxp_map_xprofile 	= BP_XProfile_Group::get( array( 'fetch_fields' => true	) );
			$bpxp_fields_group = array();
			if(!empty($bpxp_map_xprofile)){
				$bpxp_fields_group = array();
				foreach($bpxp_map_xprofile as $bpxp_mapfieldsKey => $bpxp_mapValue){
					$bpxp_profile_fields = array();
					$bpxp_fields_group[$bpxp_mapValue->name] = $bpxp_mapValue->name;
					if(!empty($bpxp_mapValue->fields)){
						foreach($bpxp_mapValue->fields as $bpxp_fieldsData){
							$bpxp_profile_fields[] = $bpxp_fieldsData->name;
						}
						$bpxp_fields_group[$bpxp_mapValue->name] = $bpxp_profile_fields;
					}
				}
			}
			/* Create HTML for current group fields */
			if(!empty($bpxp_fields_group)){
				/**
				* Start Group fields and csv x-profile fields maping
				* Create HTML and insert after file element 
				* in Member impor page
				*/
				$currentGroup = '';
				$currentGroup .= '<div class="bpxp-admin-row">';
				$currentGroup .= '<table class="bpxp-admin-table">';
				$currentGroup .= '<tr><th>Current xProfile Group Fields</th>';
				$currentGroup .= '<th>Exported xProfile Group Fields</th></tr>';
				foreach($bpxp_fields_group as $bpxp_index => $bpxp_fields){
					$currentGroup .= '<tr class="bpxp-group-heading">';
					$currentGroup .= '<td colspan="2">'.$bpxp_index.'</td></tr>';
					foreach($bpxp_fields as $bpxpKey => $bpxp_currentFields){
						$tempName = strtolower( str_replace(' ', '_', trim($bpxp_currentFields)));
						$currentGroup .= '<tr class="bpxp-group-fields"><td>'.$bpxp_currentFields;
						$currentGroup .='<input type="hidden" name="'.$tempName.'" class="bpxp_current_fields" value=""/></td>';
						if(!empty($bpxp_header)){
							$currentGroup .= '<td>';
							$currentGroup .= '<select class="bpxp_csv_fields">';
							$currentGroup .= '<option value="">--- Select CSV Fields---</option>';
							foreach($bpxp_header as $bpxp_headerVal){
								$currentGroup .= '<option value="'.$bpxp_headerVal.'">'.$bpxp_headerVal.'</option>';
							}
							$currentGroup .= '<select></td>';
						}
						$currentGroup .= '</tr>';
					}
				}
				$currentGroup .= '<br/><tr><td colspan="2"><p class="description"> <b>Note:</b> Select xProfile Fields from above to insert value for xProfile Fileds. If the fields that exist in the CSV file do not exist in your website, in that case the fields processing will be skipped, otherwise you need to create those fields..</p></td></tr>';
				$currentGroup .= '</table></div>';
			}
			_e($currentGroup, BPXP_TEXT_DOMAIN);
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

		if(isset($_POST['action']) && $_POST['action'] == 'bpxp_import_csv_data'){
			set_time_limit(0);
			$member_grp_msg = array();
			$bpxp_AllGroup = array();
			$flage = false;
			$bpxp_update_user = sanitize_text_field($_POST['bpxpj_update_user']);

			/* Get current xprofile fields */
			$bpxp_fields_index = $this->bpxp_get_current_xprofile_name();

			if(!empty($bpxp_fields_index)){
				$bpxp_fields_maping = array();
				foreach($bpxp_fields_index as $fields_index){
					$bpxp_fields_maping[$fields_index] = sanitize_text_field($_POST[$fields_index]);
				}
			}
			
			$bpxp_members_data 	= '';
			if(!empty($_POST['bpxp_csv_file'])){
				$bpxp_members_data = $_POST['bpxp_csv_file'];
			}
			 
				
			$bpxp_data_value 	= array();
			$bpxp_data_key 		= array();
			$bpxp_counter 		= 0;
			if(!empty($bpxp_members_data)){
				if($_POST['bpxpj_counter'] == 0){
					foreach($bpxp_members_data as $bpxp_member){
						foreach($bpxp_member as $data){
							if($bpxp_counter == 0){
								$bpxp_data_key[] = sanitize_text_field($data);
							}else{
								$bpxp_data_value[$bpxp_counter][] = sanitize_text_field($data);
							}
						}
						$bpxp_counter++;
					}
					
					if(in_array("user_email", $bpxp_data_key) && in_array("user_login", $bpxp_data_key)){
						update_option('bpxp_csv_headers' , $bpxp_data_key);
					}else{
						echo '<div class="bpxp-error-data">';
						_e('<p class="bpxp-error-message bpxp-message">Sorry CVS file did not imported. There are some errors in CSV column name please correct them and try again. Some columns in CSV are required, eg. user_login , user_pass, user_email, user_role.<a href="javascript:void(0)" class="bpxp-close">x</a></p></p>' , BPXP_TEXT_DOMAIN);
						echo '</div>';
						exit;
					}
					
				}else{
					foreach($bpxp_members_data as $bpxp_member){
						foreach($bpxp_member as $data){
								$bpxp_data_value[$bpxp_counter][] = sanitize_text_field($data);
						}
						$bpxp_counter++;
					}
					$bpxp_data_key = get_option('bpxp_csv_headers' , true);	
				}
			}
			/* Combine csv header as key and row data as value */
			$bpxp_users_data 		= array();

			if(!empty($bpxp_data_value)){
				foreach($bpxp_data_value as $bpxp_array){
					$bpxp_users_data[] 	= array_combine($bpxp_data_key, $bpxp_array);
				}
			}
			/* Import member data and create users */
			if(!empty($bpxp_users_data)){

				$bpxp_import_error_message = array();
				$bpxp_import_update_message = array();
				$bpxp_import_success_message = array();
				$bpxp_grp_msg = array();
				
				foreach($bpxp_users_data as $bpxp_user){
					$flage = false;
					if(!empty($bpxp_user)){
						$bpxp_userArr = array();
						$bpxp_userPass = array();
						if(array_key_exists('user_email', $bpxp_user)){
							$flage = true;
						}else{
							$flage = false;
						}
						foreach($bpxp_user as $fieldsKey => $fieldsValue){
							$userPass = '';
							if($fieldsKey == 'user_pass' && !empty($fieldsValue)){
								$bpxp_userPass[] = $fieldsValue;
							}
							$bpxp_role = '';
							/* Check if user already exists */
							if($fieldsKey == 'user_login' && !empty($fieldsValue)){
								$user_id 	= username_exists( $fieldsValue );
								$user_name 	= $fieldsValue;							
							}
							/* Create user if not exists */
							if($fieldsKey == 'user_email' && !empty($fieldsValue)){
								if(empty($user_id) && email_exists($fieldsValue) == false){
									/* Generate password */
									$bpxp_password 	= wp_generate_password( $length=12, $include_standard_special_chars=false );
									$user_email 	= $fieldsValue;
									$bpxp_userID 	= wp_create_user( $user_name, $bpxp_password, $user_email );
									$bpxp_userArr[$bpxp_userID ] = $bpxp_userID;
									$bpxp_import_success_message[] = $fieldsValue;
								} else {
									/* update existing user */
									
									if($bpxp_update_user == 'update-users'){
										$bpxp_ext_user = get_user_by('email', $fieldsValue );
										if(!empty($bpxp_ext_user)){
											$bpxp_userID = $bpxp_ext_user->data->ID;
											$bpxp_import_update_message[] = $fieldsValue;
										}
									}else{
										$bpxp_import_error_message[] = $fieldsValue;
									}
								}
							}
							/**
							* Update user meta fields
							*/
							if(!empty($bpxp_userID)){
								/* Get users role form csv data */
								if($fieldsKey == 'user_role' && !empty($fieldsValue)){
									$id = wp_update_user( array( 'ID' => $bpxp_userID, 'role' => $fieldsValue ) );
								}
								/* update user meta usre nice name */ 
								if($fieldsKey == 'user_nicename' && !empty($fieldsValue)){
									wp_update_user( array( 'ID' => $bpxp_userID, 'user_nicename', $fieldsValue)); 
								}
								/* update user meta display name */ 
								if($fieldsKey == 'display_name' && !empty($fieldsValue)){
									wp_update_user( array(  'ID' => $bpxp_userID, 'display_name', $fieldsValue)); 
								}
								/* Create password */
								if($fieldsKey == 'group_name' && !empty($fieldsValue)){
									$bpxp_grp_msg[] = $this->bpxpd_add_members_to_group($fieldsValue , $bpxp_userID);
								}
							}
						}
					}
					/* update user xprofile fields */
					if(!empty($bpxp_userArr)){
					$bpxp_xprofielID = $this->bpxp_update_user_xprofile_fields($bpxp_userArr , $bpxp_fields_maping , $bpxp_user);
						if(!empty($bpxp_userPass)){
							$id = $this->bpxp_update_user_password($bpxp_userArr , $bpxp_userPass);
						}
					}
				}
			}

			$this->bpxp_import_admin_notice($bpxp_grp_msg,'group_create');
			$this->bpxp_import_admin_notice($bpxp_import_update_message,'user_update');
			$this->bpxp_import_admin_notice($bpxp_import_error_message,'user_exists');
			$this->bpxp_import_admin_notice($bpxp_import_success_message,'user_create');
		}
		die;
	}

	

	/**
	* Display admin notice in import member page
	*
	* @since    1.0.0
	* @access   public
	* @author   Wbcom Designs
	* @return   string 
	*/
	function bpxp_import_admin_notice($bpxpNotice , $bpxpType){
		if(!empty($bpxpType)){
			$bpxpMsg 		= '';
			$containerCls 	= '';
			$boxCls 		= '';
			switch($bpxpType){
				case 'user_exists':
					$bpxpMsg 		= ' Member already exists! ';
					$containerCls 	= 'bpxp-error-data';
					$boxCls 		= 'bpxp-error-message bpxp-message';
					break;
				case 'user_create':
					$bpxpMsg 		= ' Member created successfully! ';
					$containerCls 	= 'bpxp-success-data';
					$boxCls 		= 'bpxp-success-message bpxp-message';
					break;
				case 'user_update':
					$bpxpMsg 		= ' Member updated successfully! ';
					$containerCls 	= 'bpxp-success-data';
					$boxCls 		= 'bpxp-success-message bpxp-message';
					break;
				case 'group_create':
					$bpxpMsg 		= ' Groups Not exists ! ';
					$containerCls 	= 'bpxp-error-data';
					$boxCls 		= 'bpxp-error-message bpxp-message';
					break;
				case 'rong_data':
					$bpxpMsg 		= ' ';
					$containerCls 	= 'bpxp-error-data';
					$boxCls 		= 'bpxp-error-message bpxp-message';
					break;
				default: 
					$bpxpMsg = ' Users import ';
					break;
			}
			if(!empty($bpxpType) && ($bpxpNotice)){
				if(is_array($bpxpNotice)){
					$groups = ' ';
					foreach($bpxpNotice as $key => $notice){
						if(is_array($notice)){
							foreach($notice as $grpNotice){
								echo '<div class="'.$containerCls.'">';
								_e('<p class="'.$boxCls.'">'. $grpNotice .' '. $bpxpMsg .'<a href="javascript:void(0)" class="bpxp-close">x</a></p></p>' , BPXP_TEXT_DOMAIN);
								echo '</div>';
							}
						}else{
							echo '<div class="'.$containerCls.'">';
							_e('<p class="'.$boxCls.'">'. $notice .' '. $bpxpMsg .' <a href="javascript:void(0)" class="bpxp-close">x</a></p>' , BPXP_TEXT_DOMAIN);
							echo '</div>';
						}
						
					}
				}
			}
		}
	}

	/**
	* Add user's password from CSV
	*
	* @since    1.0.0
	* @access   public
	* @author   Wbcom Designs
	* @return   string 
	*/
	public function bpxp_update_user_password($bpxpUID , $bpxpUserPass){
		if(!empty($bpxpUID) && !empty($bpxpUserPass)){
			foreach($bpxpUID as $idKey => $idVal){
				if(!empty($bpxpUserPass)){
					foreach($bpxpUserPass as $passKey => $passVal){
						 global $wpdb;
						$insert = $wpdb->query(" UPDATE `wp_users` SET `user_pass` = '$passVal' WHERE `ID` = '$idVal' ");
					}
				}	
			}
		}
	}

	/**
	* Add created user into group 
	*
	* @since    1.0.0
	* @access   public
	* @author   Wbcom Designs
	* @return   string 
	*/
	public function bpxpd_add_members_to_group($bpxpcsvGroups , $memberID){
		$groupMsg = array();
		if (!empty($bpxpcsvGroups) && strpos($bpxpcsvGroups , " - ") !== false) {
			$bpxpGrpArr = explode(" - ",$bpxpcsvGroups);
			foreach($bpxpGrpArr as $grp){
				if(!empty($grp)){
					$grpSlug 	= strtolower($grp);
					$grpID 		= BP_Groups_Group::group_exists($grpSlug);
					if(!empty($grpID) && !empty($memberID)){
						groups_join_group( $grpID , $memberID);
					}else{
						$groupMsg[] = $grp;
					}
				}
			}
		}else{
			if(!empty($bpxpcsvGroups)){
				$grpSlug 	= strtolower($bpxpcsvGroups);
				$grpID 		= BP_Groups_Group::group_exists($grpSlug);
				if(!empty($grpID) && !empty($memberID)){
					groups_join_group( $grpID , $memberID);
				}else{
					$groupMsg[] = $bpxpcsvGroups;
				}
			}
		}
		return $groupMsg;  
	}

	/**
	* Get current xprofile fields name
	*
	* @since    1.0.0
	* @access   public
	* @author   Wbcom Designs
	* @return   Array  Return all xprofile fields
	*/
	public function bpxp_get_current_xprofile_name(){
		$bpxp_current_fields 	= BP_XProfile_Group::get( array( 'fetch_fields' => true	) );
		$bpxp_fieldsName = array();
		if(!empty($bpxp_current_fields)){
			foreach($bpxp_current_fields as $bpxpKey => $bpxpValue){
				if(!empty($bpxpValue->fields)){
					foreach($bpxpValue->fields as $bpxpData){
						$bpxptemp = $bpxpData->name;
						$bpxp_fieldsName[] =  strtolower( str_replace(' ', '_', trim($bpxptemp)));
					}
				}
			}
		}
		return $bpxp_fieldsName; 
	}

	/**
	* Update user xprofile fields
	*
	* @since    1.0.0
	* @access   public
	* @author   Wbcom Designs
	*/
	public function bpxp_update_user_xprofile_fields($bpxpID , $bpxpxfields , $bpxpExpFeilds){
		$bpxpExpFeilds = array_change_key_case($bpxpExpFeilds, CASE_LOWER);
		if(!empty($bpxpID) && !empty($bpxpxfields)){
			foreach($bpxpID as $key => $id){
				foreach($bpxpxfields as $fieldkey => $fieldval){
					$tempVal = '';
					$xprKey = strtolower( str_replace( '_', ' ', trim($fieldkey)));
					if(array_key_exists($xprKey , $bpxpExpFeilds)){
						$tempVal = $bpxpExpFeilds[$xprKey];
						xprofile_set_field_data($xprKey , $id , $tempVal);
					}
				}
			}
		}
	}
}