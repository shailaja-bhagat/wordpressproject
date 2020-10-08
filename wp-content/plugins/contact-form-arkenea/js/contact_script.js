jQuery(document).ready(function(){

	var pathname = window.location.pathname; 

	currentTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
	//    currentTimezone = moment.tz.guess();
	// document.getElementById("contact_form_timezone").value = currentTimezone;
	if( pathname == "/contact-us/" ){
		document.getElementById("contact_form_timezone").value = currentTimezone;
	}

	console.log("current time: " + currentTimezone);

	//function to fetch current location with host
	var getLocation = function(href) {
		var get_host = document.createElement("a");
		get_host.href = href;
		return get_host;
	};
	var current_url = window.location.href;

	var get_current_url = getLocation(current_url);

	//remove previous URL
	window.onbeforeunload = function() {
		window.onunload = function () {
			if( previous_url != null){
				console.log("Previous URL: " + previous_url);
				localStorage.removeItem(previous_url);
			}
		}
	}

	//if previous page URL is empty
	if( document.referrer == ""){
		console.log("Current URL" + get_current_url);
		
		if(localStorage.getItem("get_current_url") == null || get_current_url == null || get_current_url == ""){
			localStorage.setItem("get_current_url", get_current_url);
		}
		
		var currentUrl = localStorage.getItem("get_current_url");
		// document.getElementById("contact_form_referral_URL").value = currentUrl;
		console.log("3. CurrentUrl: "+currentUrl);
		if( pathname == "/contact-us/" ){
			document.getElementById("contact_form_referral_URL").value = currentUrl;			
		} else {
			console.log("path1: ", pathname);
		}
	}


	// var previous_url = [];
	var previous_url = JSON.parse(localStorage.getItem("previous_url")) ? JSON.parse(localStorage.getItem("previous_url")) : [];

	//if previous page URL is not empty
	// if( document.referrer != ""){
		previous_url.push(document.referrer);

		localStorage.setItem("previous_url", JSON.stringify(previous_url));
		// if( localStorage.getItem("previous_url") == null || previous_url == null || previous_url == ""){
		// 	localStorage.setItem("previous_url", previous_url);
		// }

		var urlArray = JSON.parse(localStorage.getItem("previous_url"));
		// console.log("urlArray: "+urlArray);
		if( urlArray != "" ){
			if( pathname == "/contact-us/" ){
				document.getElementById("contact_form_referral_URL").value = urlArray;
			} else {
				console.log("path2: ", pathname);
			}
		}

	// }
});

function validateform() {
	var referrer = document.referrer;
	var iChars = "!@#$%^&*()+=-[]\\\';,./{}|\":<>?";
	
	let validationError = 0;

	jQuery('#cfa_contact_form input').each( function(){
		
		let input = jQuery(this);
		let inputVal = input.val();
		let inputId = input.attr('id');
		
		if(input.attr('type')=="text" || input.attr('type')=="email") {
			if(inputVal.trim()=="") {
				validationError++;
				jQuery(this).closest('input').addClass('validation_error');
				jQuery('#'+inputId+'_error').removeAttr('style');
			} 
			else{
				if(input.attr('type')=='email') {
					jQuery('#'+inputId+'_error').css("display", "none");
					var contact_form_email = jQuery('#contact_form_email').val();
					var filter = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
				
					if (!filter.test(contact_form_email)) {
						validationError++;
						jQuery('#errorValidcontact_form_email').show();
						jQuery(this).closest('input').addClass('validation_error');
					} else {
						jQuery('#errorValidcontact_form_email').hide();
						jQuery(this).closest('input').removeClass('validation_error');
					}
				}
				else{
					jQuery(this).closest('input').removeClass('validation_error');
					jQuery('#'+inputId+'_error').css("display", "none");
				}							
			} 
		}	            
	});

		
	if (jQuery('#contact_form_enquiry').val() == "") {
		jQuery('#contact_form_enquiry').addClass('validation_error');
		jQuery('#contact_form_enquiry_error').show();
		return false;
	} else {
		jQuery('#contact_form_enquiry_error').hide();
		jQuery('#contact_form_enquiry').removeClass('validation_error');
	}	
	
	if(validationError > 0) {
		return false;
	}
	else{
		
		jQuery('#contact_form_submit').addClass('loading');
		jQuery('.contact_form_div').addClass('loading_container');
		var data = jQuery("#cfa_contact_form").serialize();
		var redirect_page_id = jQuery('#redirect_page_id').val();
		jQuery.ajax({
			url		: contact.ajax_url,
			type	: 'post',
			data	: {
				action      : 'cfa_form_data_process',
				form_data   : data
			},
			success	: function(result){
				if(result == 'redirect_please') {
					window.location.href = redirect_page_id;
				} else {
					jQuery('.contact_form_container #result').html(result).fadeIn(500);
				}
			}
		});
	}
}

jQuery(document).ready(function(){

	jQuery("#contact_form_fname").keypress(function(event){
		var msg_first_name = jQuery(this).val();
		if (msg_first_name.length > 1){
			jQuery(this).removeClass('validation_error');
			jQuery('#contact_form_fname_error').hide();
		}else {
			jQuery(this).addClass('validation_error');
			jQuery('#contact_form_fname_error').show();
		}
		var inputValue = event.which;
		// allow letters and whitespaces only.
		if(!(inputValue >= 65 && inputValue <= 122) && (inputValue != 32 && inputValue != 0)) { 
			event.preventDefault(); 
		}
	});

	jQuery("#contact_form_lname").keypress(function(event){
		var msg_last_name = jQuery(this).val();
		if (msg_last_name.length > 1){
			jQuery(this).removeClass('validation_error');
			jQuery('#contact_form_lname_error').hide();
		}else {
			jQuery(this).addClass('validation_error');
			jQuery('#contact_form_lname_error').show();
		}
		var inputValue = event.which;
		// allow letters and whitespaces only.
		if(!(inputValue >= 65 && inputValue <= 122) && (inputValue != 32 && inputValue != 0)) { 
			event.preventDefault(); 
		}
	});

	jQuery("#contact_form_phone").keypress(function(event){
		var msg_phone_field = jQuery(this).val();
		if (msg_phone_field.length > 1){
			jQuery(this).removeClass('validation_error');
			jQuery('#contact_form_phone_error').hide();
		}else {
			jQuery(this).addClass('validation_error');
			jQuery('#contact_form_phone_error').show();
		}
		var inputValue = event.which;
		// allow numbers only.
		if(!(inputValue >= 32 && inputValue <= 64) && !(inputValue >= 91 && inputValue <= 96) && !(inputValue >= 123 && inputValue <= 126)) {
			event.preventDefault(); 
		}
	});

	jQuery("#contact_form_company").keypress(function(event){
		var msg_company_field = jQuery(this).val();
		if (msg_company_field.length > 1){
			jQuery(this).removeClass('validation_error');
			jQuery('#contact_form_company_error').hide();
		}else {
			jQuery(this).addClass('validation_error');
			jQuery('#contact_form_company_error').show();
		}
	});

	jQuery("#contact_form_enquiry").keypress(function(event){
		var msg_enquiry_field = jQuery(this).val();
		if (msg_enquiry_field.length > 1){
			jQuery(this).removeClass('validation_error');
			jQuery('#contact_form_enquiry_error').hide();
		}else {
			jQuery(this).addClass('validation_error');
			jQuery('#contact_form_enquiry_error').show();
		}
	});

	jQuery("#contact_form_email").keypress(function(event){
		var msg_email_field = jQuery(this).val();
		if (msg_email_field.length > 1){
			jQuery(this).removeClass('validation_error');
			jQuery('#contact_form_email_error').hide();
		}else {
			jQuery(this).addClass('validation_error');
			jQuery('#contact_form_email_error').show();
		}
	});

	jQuery('#cfa_contact_form').submit(function (e) {
		e.preventDefault();
		
	});

});