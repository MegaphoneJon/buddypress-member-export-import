<?php

/**
 * Provide a admin area view for Import X-Profile fields data.
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       www.wbcomdesigns.com
 * @since      1.0.0
 *
 * @package    Bp_Xprofile_Export_Import
 * @subpackage Bp_Xprofile_Export_Import/admin/partials
 */

	$bpxp_import_spinner = includes_url().'/images/spinner.gif';
?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="bpxp-admin-container">
	<h1><?php _e('Import CSV File Data' , BPXP_TEXT_DOMAIN); ?></h1>

		<div class="bpxp-admin-row bpxp-limit">
			<div class="bpxp-admin-3 bpxp-admin-label">
				<label for="bpxp_xprofile_fields"><?php _e('CSV Chunk Limit', BPXP_TEXT_DOMAIN);?></label>
			</div>
			<div class="bpxp-admin-3">
				<input type="number" name="bpxp_set_member_limit" id="bpxp_set_member_limit" value="<?php _e(10 , BPXP_TEXT_DOMAIN); ?>" />
				<p class="description"><?php _e('This is the number of rows in the CSV file that get grouped by the value that is saved above, eg. 10. This means that the complete number of rows will be chunked and processed.', BPXP_TEXT_DOMAIN); ?></p>
			</div>
		</div>

		<div class="bpxp-admin-row" id="upload_csv">
			<div class="bpxp-admin-3 bpxp-admin-label">
				<label for="bpxp_xprofile_fields"><?php _e('Uploade CSV File', BPXP_TEXT_DOMAIN);?></label>
			</div>
			<div class="bpxp-admin-3">
				<input type="file" name="bpxp_import_file" id="bpxp_import_file" value="" />
			</div>
			<div class="bpxp-admin-3">
				<img src="<?php echo $bpxp_import_spinner;?>" class="bpxp-admin-settings-spinner" />
			</div>
		</div>

		<div class="bpxp-admin-row">
			<div class="bpxp-admin-3 bpxp-admin-label">
				<label for="bpxp_update_user"><?php _e('Update Users Data', BPXP_TEXT_DOMAIN);?></label>
			</div>
			<div class="bpxp-admin-3">
				<input type="checkbox" name="bpxp_update_user" id="bpxp_update_user" value="bpxp-create-user" />
				<span><?php _e('Enable checkbox to update existing users data' , BPXP_TEXT_DOMAIN); ?></span>
			</div>
		</div>

		<div class="bpxp-admin-row">
			<div class="bpxp-admin-3">
				<input type="submit" name="bpxp_import_xprofile_data" id="bpxp_import_xprofile_data" class="bpxp-admin-control button button-primary"  value="<?php _e('Import' , BPXP_TEXT_DOMAIN); ?>" />
			</div>
			<div class="bpxp-admin-3">
			<img src="<?php echo $bpxp_import_spinner;?>" class="bpxp-admin-button-spinner" />
			</div>
		</div>

		<div class="bpxp-admin-row">
		<p><?php _e( "<b> Note: </b> Please remove all extra rows from csv file. CSV file must have column name in first row." , BPXP_TEXT_DOMAIN); ?></p>
		</div>
</div>