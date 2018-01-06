(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
	 /*
	 * Get User fields type on radio button change
	 * and display into filds type select box
	 */

	/*--------------------------------------------------------------
	  checked all check box on select all box clicked
	---------------------------------------------------------------*/
	jQuery( document ).on(
		'change', '.bpxp-all-selected' , function(){
			if (jQuery( this ).is( ':checked' )) {
				jQuery( this ).parent().nextAll().children().prop( 'checked', true );
			} else {
				jQuery( this ).parent().nextAll().children().prop( 'checked', false );
			}
		}
	);

	 /*----------------------------------------------------------------
	unchecked select all if any checkbox is uncheck in select member
	------------------------------------------------------------------*/
	jQuery( document ).on(
		'change', '.bpxp-single-member' , function(){
			if (this.checked == false) {
				jQuery( ".bpxp-all-member" )[0].checked = false;
			}
			if (jQuery( '.bpxp-single-member:checked' ).length == jQuery( '.bpxp-single-member' ).length ) {
				jQuery( ".bpxp-all-member" )[0].checked = true;
			}
		}
	);

	/*----------------------------------------------------------------------
	unchecked select all if any checkbox is uncheck in select fields group
	------------------------------------------------------------------------*/
	jQuery( document ).on(
		'change', '.bpxp-single-group' , function(){
			if (this.checked == false) {
				jQuery( ".bpxp-all-group" )[0].checked = false;
			}
			if (jQuery( '.bpxp-single-group:checked' ).length == jQuery( '.bpxp-single-group' ).length ) {
				jQuery( ".bpxp-all-group" )[0].checked = true;
			}
		}
	);

	/*----------------------------------------------------------------------
	unchecked select all if any checkbox is uncheck in select profile fields
	------------------------------------------------------------------------*/
	jQuery( document ).on(
		'change', '.bpxp-single-profile' , function(){
			if (this.checked == false) {
				jQuery( ".bpxp-all-profile" )[0].checked = false;
			}
			if (jQuery( '.bpxp-single-profile:checked' ).length == jQuery( '.bpxp-single-profile' ).length ) {
				jQuery( ".bpxp-all-profile" )[0].checked = true;
			}
		}
	);

	/*--------------------------------------------------------------
	  Send ajax request to get xprofile fields by group id
	--------------------------------------------------------------*/
	jQuery( document ).on(
		'change' , "input[name='bpxp_field_group[]']" , function(){
			var bpxp_field_group_id = jQuery( "input[name='bpxp_field_group[]']" )
			.map(
				function(){
					if (jQuery( this ).is( ':checked' )) {
						return jQuery( this ).val();
					}
				}
			).get();
			jQuery( '.bpxp-admin-settings-spinner' ).css( 'display' , 'block' );
			jQuery.post(
				bpxp_ajax_url.ajaxurl,
				{
					'action'    			: 'bpxp_get_export_xprofile_fields',
					'bpxp_field_group_id'	: bpxp_field_group_id
				},
				function(response) {
					jQuery( '#bpxp_xprofile_fileds_data' ).html( response );
					jQuery( '.bpxp-admin-settings-spinner' ).css( 'display' , 'none' );
				}
			);
		}
	);

	/*-------------------------------------------------------------
	* Disable Import button until select an CSV file.
	*------------------------------------------------------------*/
	jQuery( window ).on(
		'load' , function(){
			jQuery( '#bpxp_import_xprofile_data' ).attr( "disabled", "disabled" );
		}
	);

	jQuery( document ).on(
		'click' , '#bpxp_header_close' , function(){
			jQuery( '#csv_header_error' ).hide();
		}
	);
	/*-------------------------------------------------------------
	* Read CSV file header fields
	*------------------------------------------------------------*/
	var bpxpj_csvData = new Array();
	jQuery( document ).on(
		'change' , "#bpxp_import_file" , function(e){
			jQuery( '#csv_header_error' ).hide();
			jQuery( '.bpxp-maping' ).remove();
			jQuery( '.bpxp-error-data' ).remove();
			jQuery( '.bpxp-success-data' ).remove();
			var bpxpj_ext = jQuery( "input#bpxp_import_file" ).val().split( "." ).pop().toLowerCase();

			if (jQuery.inArray( bpxpj_ext, ["csv"] ) == -1) {
				jQuery( '#bpxp-fields-maping' ).remove();
				jQuery( '#bpxp_import_message' ).addClass( 'bpxp-error-message' );
				jQuery( '#bpxp_import_message' ).html( 'Please Select CSV File.' );
				return false;
			}
			if (e.target.files != undefined) {
				var bpxpj_reader    = new FileReader();
				bpxpj_reader.onload = function(e) {
					var bpxpj_csvHeader = e.target.result.split( "\n" );
					var bpxpj_headerVal = bpxpj_csvHeader[0].split( "," );

					if (jQuery.inArray( 'user_email', bpxpj_headerVal ) != -1 && jQuery.inArray( 'user_login', bpxpj_headerVal ) != -1) {
						for (var i = 0; i < bpxpj_csvHeader.length; i++) {
							var temp = bpxpj_csvHeader[i].split( "," );
							bpxpj_csvData.push( temp );
						}
						console.log( bpxpj_csvData );
						jQuery( '.bpxp-admin-settings-spinner' ).css( 'display' , 'block' );
						jQuery.post(
							bpxp_ajax_url.ajaxurl,
							{
								'action'    			: 'bpxp_import_header_fields',
								'bpxp_csv_header'		: bpxpj_headerVal
							},
							function(response) {
								console.log( response );
								jQuery( '#upload_csv' ).after( response );
								jQuery( '#bpxp_import_xprofile_data' ).removeAttr( "disabled", "disabled" );
								jQuery( '.bpxp-admin-settings-spinner' ).css( 'display' , 'none' );
							}
						);

					} else {
						jQuery( '#csv_header_error' ).show();
					}
				};
				bpxpj_reader.readAsText( e.target.files.item( 0 ) );
			}
			return false;
		}
	);

	var bpxpj_req_counter = 0;
	/*var bpxpj_chunk_size 	= 10;*/
	var bpxpj_index_pointer   = 0;
	var bpxpj_no_more_request = false;
	function bpxpj_send_chunk_csv_data( bpxpj_field, bpxpj_update_user , bpxpj_chunk_size ) {
		var chunk_csv_data = bpxpj_csvData.slice( bpxpj_index_pointer , bpxpj_index_pointer + bpxpj_chunk_size );
		jQuery.post(
			bpxp_ajax_url.ajaxurl,
			{
				'action'    		: 'bpxp_import_csv_data',
				'bpxp_csv_file'		: chunk_csv_data,
				'bpxpj_update_user' : bpxpj_update_user,
				'bpxpj_counter' 	: bpxpj_req_counter,
				'bpxpj_field' 		: bpxpj_field
			},
			function(response) {
				jQuery( '.bpxp-limit' ).before( response );
				jQuery( '.bpxp-maping' ).remove();

				bpxpj_req_counter++;
				bpxpj_index_pointer += bpxpj_chunk_size;

				if ( bpxpj_csvData.length <= bpxpj_index_pointer ) {
					bpxpj_no_more_request = true;
				}

				if ( ! bpxpj_no_more_request ) {
					bpxpj_send_chunk_csv_data( bpxpj_field, bpxpj_update_user , bpxpj_chunk_size );
				} else {
					jQuery( '.bpxp-admin-button-spinner' ).css( 'display' , 'none' );
				}
			}
		);
	}

	jQuery( document ).on(
		'click' , "#bpxp_import_xprofile_data" , function(e){
			jQuery( '.bpxp-admin-button-spinner' ).css( 'display' , 'block' );
			var bpxpj_field = {};
			jQuery( '.bpxp_current_fields' ).each(
				function(){
					bpxpj_field[jQuery( this ).attr( 'name' )] = jQuery( this ).val();
				}
			);

			if (bpxpj_csvData != '') {
				var bpxpj_update_user = '';
				if (jQuery( 'input[name="bpxp_update_user"]:checked' ).length > 0) {
					bpxpj_update_user = 'update-users';
				}
				var tempChunk = parseInt( jQuery( '#bpxp_set_member_limit' ).val() );

				var i , j , chunk_csv_data;

				bpxpj_send_chunk_csv_data( bpxpj_field, bpxpj_update_user , tempChunk );
				return;
			}
			jQuery( '.bpxp-admin-button-spinner' ).css( 'display' , 'block' );
			return false;
		}
	);

	jQuery( document ).on(
		'click' , '.bpxp-close' , function(){
			jQuery( this ).parent().parent().remove();
		}
	);

	/*----------------------------------------------------------
	Insert CSV file group fields data into current fields on change
	-----------------------------------------------------------*/
	jQuery( document ).on(
		'change' , '.bpxp_csv_fields' , function(){
			var bpxpj_field_val = jQuery( this ).val();
			jQuery( this ).parent().prev( 'td' ).children().val( bpxpj_field_val );
		}
	);

	/*--------------------------------------------------------------
	  Export Buddypress member data
	--------------------------------------------------------------*/
	jQuery( document ).on(
		'click', '#bpxp_export_xprofile_data', function(){
			jQuery( '#bpxp_export_message' ).hide();
			jQuery( '#bpxp_export_message' ).hide();
			var bpxpj_bpmember = jQuery( "input[name='bpxp_bpmember[]']" )
			.map(
				function(){
					if (jQuery( this ).is( ':checked' )) {
						return jQuery( this ).val();
					}
				}
			).get();

			var bpxpj_field_group = jQuery( "input[name='bpxp_field_group[]']" )
			.map(
				function(){
					if (jQuery( this ).is( ':checked' )) {
						return jQuery( this ).val();
					}
				}
			).get();

			var bpxpj_xprofile_fields = jQuery( "input[name='bpxp_xprofile_fields[]']" )
			.map(
				function(){
					if (jQuery( this ).is( ':checked' )) {
						return jQuery( this ).val();
					}
				}
			).get();
			if (bpxpj_bpmember == '') {
				jQuery( '#bpxp_user_xprofile' ).addClass( 'bpxp-error-border' );
			} else {
				jQuery.post(
					bpxp_ajax_url.ajaxurl,
					{
						'action'    			: 'bpxp_export_xprofile_data',
						'bpxpj_bpmember'		: bpxpj_bpmember,
						'bpxpj_field_group'		: bpxpj_field_group,
						'bpxpj_xprofile_fields'	: bpxpj_xprofile_fields
					},
					function(response) {
						console.log( response );
						jQuery( '#bpxp_export_fields' ).before( '<p id="bpxp_export_message" class="bpxp-success-message bpxp-message"> Successfully! CSV File Exported </p>' );
						JSONToCSVConvertor( response );
					}
				);
			}
		}
	);

	jQuery( document ).on(
		'click' , '.bpxp-export' , function(){
			jQuery( '#bpxp_user_xprofile' ).removeClass( 'bpxp-error-border' );
			jQuery( '#bpxp_export_message' ).hide();
		}
	);

	/*---------------------------------------------------
	 Add xprofile fields name into CSV profile fields
	------------------------------------------------------*/
	jQuery( document ).on(
		'change' , '.bpxp_csv_fields' , function(){
			var fields = jQuery( this ).find( ":selected" ).text();
			jQuery( this ).prev().val( fields );
		}
	);

	/*--------------------------------------------------------------
	  Add admin notice on import data
	--------------------------------------------------------------*/
	jQuery( document ).ready(
		function(){
			var bpxpj_error = jQuery( '.bpxp-error-data' ).html();
			var bpxpj_msg   = jQuery( '.bpxp-success-data' ).html();
			jQuery( '.import-data' ).append( bpxpj_msg );
			jQuery( '.import-data' ).append( bpxpj_error );
			jQuery( '.bpxp-error' ).addClass( 'bpxp-error-message' );
			jQuery( '.bpxp-msg' ).addClass( 'bpxp-success-message' );
		}
	);

	/*--------------------------------------------------------------
	  Display Checkboxes on select box click
	--------------------------------------------------------------*/
	var bpxp_user_expanded = false;
	jQuery( document ).on(
		'click' , '.bpxp-selectBox' , function(){
			if ( ! bpxp_user_expanded) {
				jQuery( this ).next().css( 'display' , 'block' );
				bpxp_user_expanded = true;
			} else {
				jQuery( this ).next().css( 'display' , 'none' );
				bpxp_user_expanded = false;
			}
		}
	);

})( jQuery );

function JSONToCSVConvertor(JSONData){
	var allData  = typeof JSONData != 'object' ? JSON.parse( JSONData ) : JSONData;
	var dataText = "";
	var rowText  = "";
	var fileName = "Member-data";

	for (var index in allData[0]) {
		rowText += index + ',';
	}

	dataText += rowText + '\r\n';

	for (var i = 0; i < allData.length; i++) {
		var rowText = "";

		for (var index in allData[i]) {
			rowText += '' + allData[i][index] + ',';
		}

		rowText.slice( 0, rowText.length - 1 );
		dataText += rowText + '\r\n';
	}

	var isIE = false || ! ! document.documentMode;
	if (isIE) {
		var IEwindow = window.open();
		IEwindow.document.write( 'sep=,\r\n' + dataText );
		IEwindow.document.close();
		IEwindow.document.execCommand( 'SaveAs', true, fileName + ".csv" );
		IEwindow.close();
	} else {
		var uri       = 'data:text/csv;charset=utf-8,' + escape( dataText );
		var link      = document.createElement( "a" );
		link.href     = uri;
		link.style    = "visibility:hidden";
		link.download = fileName + ".csv";
		document.body.appendChild( link );
		link.click();
		document.body.removeChild( link );
	}
}
