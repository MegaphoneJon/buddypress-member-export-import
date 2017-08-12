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
	 jQuery(document).on('change', '.bpxp-all-selected' , function(){
	 	if(jQuery(this).is(':checked')){
	 		jQuery(this).parent().nextAll().children().prop('checked', true);
	 	}else{
	 		jQuery(this).parent().nextAll().children().prop('checked', false);
	 	}
	 });
	 
	/*--------------------------------------------------------------
	  Send ajax request to get xprofile fields by group id 
	--------------------------------------------------------------*/
	jQuery(document).on('change' , "input[name='bpxp_field_group[]']" , function(){
	 	var bpxp_field_group_id = jQuery("input[name='bpxp_field_group[]']")
          .map(function(){
          	if(jQuery(this).is(':checked')){
          		return jQuery(this).val();
          	}
        }).get();
        jQuery('.bpxp-admin-settings-spinner').css('display' , 'block');
        jQuery.post(
	        bpxp_ajax_url.ajaxurl,
	        {
	            'action'    			: 'bpxp_get_export_xprofile_fields', 
	            'bpxp_field_group_id'	: bpxp_field_group_id                                   
	        },
        function(response) {
           jQuery('#bpxp_xprofile_fileds_data').html(response);
           jQuery('.bpxp-admin-settings-spinner').css('display' , 'none');                      
        });
	});

	/*-------------------------------------------------------------
	* Disable Import button until select an CSV file.
	*------------------------------------------------------------*/
	jQuery(window).on('load' , function(){
		jQuery('#bpxp_import_xprofile_data').attr("disabled", "disabled");
	});

	/*-------------------------------------------------------------
	* Read CSV file header fields
	*------------------------------------------------------------*/
	var bpxpj_csvData = new Array();
	jQuery(document).on('change' , "#bpxp_import_file" , function(e){
		var bpxpj_ext = jQuery("input#bpxp_import_file").val().split(".").pop().toLowerCase();

		if(jQuery.inArray(bpxpj_ext, ["csv"]) == -1) {
			jQuery('#bpxp_import_message').addClass('bpxp-error-message');
			jQuery('#bpxp_import_message').html('Please Select CSV File.');
			return false;
		}
		if (e.target.files != undefined) {
			var bpxpj_reader = new FileReader();
			bpxpj_reader.onload = function(e) {
				var bpxpj_csvHeader 	= e.target.result.split("\n");
				var bpxpj_headerVal		= bpxpj_csvHeader[0].split(",");
				for(var i = 0 ; i < bpxpj_csvHeader.length; i++){
					var temp = bpxpj_csvHeader[i].split(",");
					bpxpj_csvData.push(temp);
				}
				jQuery('.bpxp-admin-settings-spinner').css('display' , 'block');
				jQuery.post(
	            bpxp_ajax_url.ajaxurl,
	                {
	                'action'    			: 'bpxp_import_header_fields',
	                'bpxp_csv_header'		: bpxpj_headerVal                                 
	                },
	                function(response) {
	                   	console.log(response); 
	                   	jQuery('#upload_csv').after(response);
	                   	jQuery('#bpxp_import_xprofile_data').removeAttr("disabled", "disabled");
	                   	jQuery('.bpxp-admin-settings-spinner').css('display' , 'none');                      
	                }
	        	);
			};
			bpxpj_reader.readAsText(e.target.files.item(0));
		}
		return false;
	});


	jQuery(document).on('click' , "#bpxp_import_xprofile_data" , function(e){
		jQuery('.bpxp-admin-button-spinner').css('display' , 'block');
		if(bpxpj_csvData != ''){
			var bpxpj_update_user = '';
			if(jQuery('input[name="bpxp_update_user"]:checked').length > 0){
				bpxpj_update_user = 'update-users';
			}
			var tempChunk 			= parseInt(jQuery('#bpxp_set_member_limit').val());

			var i , j , chunk_csv_data;

			var bpxpj_counter = 0;

			for ( i = 0, j = bpxpj_csvData.length; i < j; i += tempChunk ){
				jQuery('.bpxp-admin-button-spinner').css('display' , 'block');
			    chunk_csv_data = bpxpj_csvData.slice( i , i + tempChunk);
			    jQuery.post(
				bpxp_ajax_url.ajaxurl,
				    {
				    'action'    		: 'bpxp_import_csv_data',
				    'bpxp_csv_file'		: chunk_csv_data,
				    'bpxpj_update_user' : bpxpj_update_user,
				    'bpxpj_counter' 	: bpxpj_counter                              
				    },
				    function(response) {
				       	console.log(response);
				       	jQuery('.bpxp-limit').before(response); 
				       	//alert(tempChunk + 'Member data import do you want import another ' + tempChunk);
				       	jQuery('.bpxp-admin-button-spinner').css('display' , 'none');                      
				    }
				);
				bpxpj_counter++;
			}
		}
       	return false;
	});

	jQuery(document).on('click' , '.bpxp-close' , function(){
		jQuery(this).parent().parent().remove();
	});

	/*----------------------------------------------------------
	Insert CSV file group fields data into current fields on change
	-----------------------------------------------------------*/
	jQuery(document).on('change' , '.bpxp_csv_fields' , function(){
		var bpxpj_field_val = jQuery(this).val();
		jQuery(this).parent().prev('td').children().val(bpxpj_field_val);	
	});


    /*--------------------------------------------------------------
	  Export Buddypress member data 
	--------------------------------------------------------------*/
	jQuery(document).on('click', '#bpxp_export_xprofile_data', function(){ 

		var bpxpj_bpmember = jQuery("input[name='bpxp_bpmember[]']")
          .map(function(){
          	if(jQuery(this).is(':checked')){
          		return jQuery(this).val();
          	}
        }).get();

        var bpxpj_field_group = jQuery("input[name='bpxp_field_group[]']")
          .map(function(){
          	if(jQuery(this).is(':checked')){
          		return jQuery(this).val();
          	}
        }).get();

        var bpxpj_xprofile_fields = jQuery("input[name='bpxp_xprofile_fields[]']")
          .map(function(){
          	if(jQuery(this).is(':checked')){
          		return jQuery(this).val();
          	}
        }).get();
        if(bpxpj_bpmember == ''){
        	jQuery('.bpxp-bpuser').addClass('bpxp-error-border');
        }
        if(bpxpj_field_group == ''){
        	jQuery('.bpxp-fieldsgroup').addClass('bpxp-error-border');
        }
        if(bpxpj_xprofile_fields == ''){
        	jQuery('.bpxp-xprofile').addClass('bpxp-error-border');
        }
        if(bpxpj_xprofile_fields != '' && bpxpj_field_group != '' && bpxpj_bpmember != ''){
        	jQuery.post(
            bpxp_ajax_url.ajaxurl,
                {
                'action'    			: 'bpxp_export_xprofile_data',
                'bpxpj_bpmember'		: bpxpj_bpmember, 
                'bpxpj_field_group'		: bpxpj_field_group, 
                'bpxpj_xprofile_fields'	: bpxpj_xprofile_fields                                   
                },
                function(response) {
                   	console.log(response);  
                   	jQuery('#bpxp_export_message').addClass('bpxp-success-message');

                   	var tasks = response;
					JSONToCSVConvertor( tasks, "Buddypress Member Data ", true );
        			jQuery('#bpxp_export_message').html('Successfully! CSV File Exported');                    
                }
        	);
        }else{
        	jQuery('#bpxp_export_message').addClass('bpxp-error-message');
        	jQuery('#bpxp_export_message').html('Please Select All Options');
        }
        
    });

	/*--------------------------------------------------------------
	  Add admin notice on import data
	--------------------------------------------------------------*/
	jQuery(document).ready(function(){
		var bpxpj_error = jQuery('.bpxp-error-data').html();
		var bpxpj_msg   = jQuery('.bpxp-success-data').html();
		jQuery('.import-data').append(bpxpj_msg);
		jQuery('.import-data').append(bpxpj_error);
		jQuery('.bpxp-error').addClass('bpxp-error-message');
		jQuery('.bpxp-msg').addClass('bpxp-success-message');
	});

	/*--------------------------------------------------------------
	  Display Checkboxes on select box click 
	--------------------------------------------------------------*/
	var bpxp_user_expanded = false;
	jQuery(document).on('click' , '.bpxp-selectBox' , function(){
		if (!bpxp_user_expanded) {
			jQuery(this).next().css('display' , 'block');
			bpxp_user_expanded = true;
		}else{
			jQuery(this).next().css('display' , 'none');
			bpxp_user_expanded = false;
		}
	});

})( jQuery );


function JSONToCSVConvertor( JSONData, ReportTitle, ShowLabel ) {
	var arrData = typeof JSONData != 'object' ? JSON.parse(JSONData) : JSONData;
	var CSV = '';
	CSV += ReportTitle + '\r\n';
	if (ShowLabel) {
		var row = "";
		for (var index in arrData[0]) {
			row += index + ',';
		}
		row = row.slice(0, -1);
		CSV += row + '\r\n';
	}

	//1st loop is to extract each row
	for (var i = 0; i < arrData.length; i++) {
		var row = "";
		for (var index in arrData[i]) {
			row += '"' + arrData[i][index] + '",';
		}
		row.slice(0, row.length - 1);
		//add a line break after each row
		CSV += row + '\r\n';
	}

	if (CSV == '') {
		jQuery('#bpxp_export_message').addClass('bpxp-error-message');
        jQuery('#bpxp_export_message').html('Invaid Data into CSV file..');
		return;
	}

	var fileName 	= "CSV";
	fileName 		+= ReportTitle.replace(/ /g,"_");
	var uri 		= 'data:text/csv;charset=utf-8,' + escape(CSV);
	var link 		= document.createElement("a");
	link.href 		= uri;
	link.style 		= "visibility:hidden";
	link.download 	= fileName + ".csv";
	document.body.appendChild(link);
	link.click();
	document.body.removeChild(link);
}