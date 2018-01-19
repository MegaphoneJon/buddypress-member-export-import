<?php
/**
 * Provide a admin area view for Export X-Profile fields data.
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       www.wbcomdesigns.com
 * @since      1.0.0
 *
 * @package    Bp_Xprofile_Export_Import
 * @subpackage Bp_Xprofile_Export_Import/admin/partials
 */
?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<?php
$bpxp_spinner         = includes_url() . '/images/spinner.gif';
$bpxp_user            = get_users();
$bpxp_xprofile_fields = BP_XProfile_Group::get( array( 'fetch_fields' => true ) );
$bpxp_fields_group    = array();
if ( ! empty( $bpxp_xprofile_fields ) ) {
	$bpxp_profile_fields = array();
	foreach ( $bpxp_xprofile_fields as $bpxp_fields_key => $bpxp_fields_value ) {
		$bpxp_fields_group[ $bpxp_fields_value->id ] = $bpxp_fields_value->name;
		if ( ! empty( $bpxp_fields_value->fields ) ) {
			foreach ( $bpxp_fields_value->fields as $bpxp_fields_data ) {
				$bpxp_profile_fields[] = array(
					'name' => $bpxp_fields_data->name,
					'id'   => $bpxp_fields_data->id,
				);
			}
		}
	}
}
?>
<div class="bpxp-admin-container">
	<h1><?php _e( 'Export Buddypress Members Data', 'bp-xprofile-export-import' ); ?></h1>

	<?php do_action( 'bpxp_before_export_select_user' ); ?>
	<div id="bpxp_export_fields" class="bpxp-admin-row">
		<div class="bpxp-admin-3 bpxp-admin-label">
			<label for="bpxp_user_xprofile"><?php _e( 'Select Users', 'bp-xprofile-export-import' ); ?></label>
		</div>
		<div class="bpxp-admin-3">
			<div class="bpxp-multiselect">
				<div class="bpxp-selectBox">
					<select name="bpxp_user_xprofile" id="bpxp_user_xprofile">
						<option value=""><?php _e( 'Select Users', 'bp-xprofile-export-import' ); ?></option>
					</select>
					<div class="bpxp-overSelect bpxp-bpuser"></div>
				</div>
				<div class="bpxp-checkboxes" id="bpxp_all_user_checkbox">
					<label for="bpxp_all_user">
						<input type="checkbox" class="bpxp-all-selected bpxp-all-member bpxp-export" name="bpxp_bpmember[]" value="bpxp-all-user"/>
						<?php _e( 'All Users', 'bp-xprofile-export-import' ); ?>
					</label>
					<?php
					if ( ! empty( $bpxp_user ) ) {
						foreach ( $bpxp_user as $bpxp_admin_data => $bpxp_admin_value ) {
						?>
							<label for="<?php echo $bpxp_admin_value->data->user_login; ?>">
								<input type="checkbox" class="bpxp-export bpxp-single-member" name="bpxp_bpmember[]" value="<?php echo $bpxp_admin_value->data->ID; ?>"/>
								<?php _e( $bpxp_admin_value->data->display_name, 'bp-xprofile-export-import' ); ?>
							</label>
							<?php
						}
					}
					?>
				</div>
			</div>
			<p class="description">
				<?php _e( "Select the user's to export the data of.", 'bp-xprofile-export-import' ); ?>
			</p>
		</div>
	</div>
	<?php do_action( 'bpxp_after_export_select_user' ); ?>

	<?php do_action( 'bpxp_before_export_fields_group' ); ?>
	<div class="bpxp-admin-row">
		<div class="bpxp-admin-3 bpxp-admin-label">
			<label for="bpxp_fields_group"><?php _e( 'Fields Group', 'bp-xprofile-export-import' ); ?></label>
		</div>
		<div class="bpxp-admin-3">
			<div class="bpxp-multiselect">
				<div class="bpxp-selectBox">
					<select id="bpxp_xprofile_group">
						<option value=""><?php _e( 'Select Fields Group', 'bp-xprofile-export-import' ); ?></option>
					</select>
				<div class="bpxp-overSelect bpxp-fieldsgroup"></div>
				</div>
				<div class="bpxp-checkboxes">
					<label for="all-fields-group">
						<input type="checkbox" name="bpxp_field_group[]" value="all-fields-group" class="bpxp-all-selected bpxp-all-group"/>
						<?php _e( 'All Fields Group', 'bp-xprofile-export-import' ); ?>
					</label>
					<?php
					if ( ! empty( $bpxp_fields_group ) ) {
						foreach ( $bpxp_fields_group as $bpxp_group_id => $bpxp_group_data ) {
						?>
							<label for="<?php echo $bpxp_group_data; ?>">
								<input type="checkbox" class="bpxp-single-group" name="bpxp_field_group[]" value="<?php echo $bpxp_group_id; ?>"/>
								<?php _e( $bpxp_group_data, 'bp-xprofile-export-import' ); ?>
							</label>
							<?php
						}
					}
					?>
				</div>
			</div>
			<p class="description">
				<?php _e( 'Select the Fields Group to export the data of.', 'bp-xprofile-export-import' ); ?>
			</p>
		</div>
	</div>
	<?php do_action( 'bpxp_after_export_fields_group' ); ?>

	<?php do_action( 'bpxp_before_export_prof_fields' ); ?>
	<div class="bpxp-admin-row">
		<div class="bpxp-admin-3 bpxp-admin-label">
			<label for="bpxp_xprofile_fields"><?php _e( 'Select xProfile Fields', 'bp-xprofile-export-import' ); ?></label>
		</div>
		<div class="bpxp-admin-3">
			<div class="bpxp-multiselect">
				<div class="bpxp-selectBox">
					<select id="bpxp_xprofile_filed">
						<option value=""><?php _e( 'Select xProfile Fields', 'bp-xprofile-export-import' ); ?></option>
					</select>
				<div class="bpxp-overSelect bpxp-xprofile"></div>
				</div>
				<div class="bpxp-checkboxes" id="bpxp_xprofile_fileds_data">
					<label for="bpxp-msg">
						<?php _e( 'Select Fields Group Type First.', 'bp-xprofile-export-import' ); ?>
					</label>
				</div>
			</div>
			<p class="description">
				<?php _e( 'Select the xProfile Fields to export the data of.', 'bp-xprofile-export-import' ); ?>
			</p>
		</div>
		<div class="bpxp-admin-3">
			<img src="<?php echo $bpxp_spinner; ?>" class="bpxp-admin-settings-spinner" />
		</div>
	</div>
	<?php do_action( 'bpxp_after_export_prof_fields' ); ?>

	<div class="bpxp-admin-row">
		<div class="bpxp-admin-3">
			<input type="submit" name="bpxp_export_xprofile_data" id="bpxp_export_xprofile_data" class="bpxp-admin-control button button-primary"  value="<?php _e( 'Export', 'bp-xprofile-export-import' ); ?>" />
		</div>
		<?php do_action( 'bpxp_after_export_buttons' ); ?>
		<div class="bpxp-admin-3">
		</div>
	</div>
</div>
