<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link    https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package thrive-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}

?>
<!doctype html>
<html <?php language_attributes(); ?><?php thrive_html_class(); ?>>
<head>
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<?php wp_head(); ?>
	<?php Thrive\Theme\AMP\Main::print_amp_permalink(); ?>
	<script>

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
		
	</script>

</head>

<body <?php body_class( '' ); ?>>
