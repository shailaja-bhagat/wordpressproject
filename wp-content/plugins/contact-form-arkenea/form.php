<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//define vars
$cfa_option         = get_option('cfa_option');

$to			        =	$cfa_option['form']['email'];
$redirect_page_id   =	$cfa_option['form']['thanks'];
$email_subject      =	$cfa_option['form']['subject'];
$bcc_first          =	$cfa_option['form']['bcc_first'];
$bcc_second         =	$cfa_option['form']['bcc_second'];

$google_sitekey_local =	isset($cfa_option['go']['local_sitekey']) ? $cfa_option['go']['local_sitekey'] : '';
$google_secretkey_local =	isset($cfa_option['go']['local_secretkey']) ? $cfa_option['go']['local_secretkey'] : '';

$google_sitekey_live =	isset($cfa_option['go']['live_sitekey']) ? $cfa_option['go']['live_sitekey'] : '';
$google_secretkey_live =	isset($cfa_option['go']['live_secretkey']) ? $cfa_option['go']['live_secretkey'] : '';

if( (!isset($google_secretkey_live) &&  !isset($google_sitekey_live))
    || (!isset($google_secretkey_local) &&  !isset($google_sitekey_local) ) ){
        echo '<div style="color: red;font-size: 12px;">You have not added Google Recaptcha Keys to your shortcode. Check readme.txt file for more information.</div>';
    }

$whitelist = array( '127.0.0.1', '::1' );
if( in_array( $_SERVER['REMOTE_ADDR'], $whitelist) ) {
    $server = 'local';
} else {
    $server = 'live';
}

?>

<div class="contact_form_container">
    <div id="result"></div>
    <form name="contact_form" class="customContactForm" id="cfa_contact_form" method="post" action="<?php the_permalink(); ?>" >

    <div class="control_row col_parent">
            <div class="control_box w50">
                <input id="contact_form_fname" name="contact_form_fname" type="text" placeholder="First Name" class="cf_reqMsg"/>
                <p class="loginError" id="contact_form_fname_error" style="float:none;display:none">The field is required*</p>
            </div>
            <div class="control_box w50">
                <input id="contact_form_lname" name="contact_form_lname" type="text" placeholder="Last Name" class="cf_reqMsg"/>
                <p class="loginError" id="contact_form_lname_error" style="float:none;display:none">The field is required*</p>
            </div>
            <div class="control_box w50">
                <input id="contact_form_email" type="email" name="contact_form_email" placeholder="Email"  class="cf_reqMsg"/>
                <p class="loginError" style="float:none;display:none" id="contact_form_email_error" >The field is required*</p>
                <p class="loginError" style="float:none;display:none" id="errorValidcontact_form_email">The e-mail address entered is invalid</p>
            </div>
        </div>

        <div class="control_row col_parent">
            <div class="control_box w50">
                <input id="contact_form_phone" type="text" name="contact_form_phone" placeholder="Phone" maxlength = "18" class="cf_reqMsg"/>
                <p class="loginError" style="float: none;display:none" id="contact_form_phone_error" >The field is required*</p>
            </div>            
            <div class="control_box w50">
                <input id="contact_form_company" name="contact_form_company" type="text" placeholder="Company/Project"  class="cf_reqMsg"/>
                <p class="loginError" style="float: none;display:none" id="contact_form_company_error" >The field is required*</p>
            </div>
        </div>

        <div class="control_row col_parent">
            <div class="control_box w50 message_box">
                <textarea width="100%" name="contact_form_enquiry" id="contact_form_enquiry" class="inputText-enquiry enq-textarea cf_reqMsg"  placeholder="Message" ></textarea>
                <p class="loginError" style="float:none;display:none" id="contact_form_enquiry_error">The field is required*</p>
            </div>
            <?php
                //echo $_SERVER['HTTP_REFERER'];
            ?>
            <div class="control_box w50 captcha_submit_box">
               
                <div class="contact_form_div submit_box">
                    <input type="submit" name="submit" value="Send" id="contact_form_submit" class="btn_theme all_form_submit green-btn width-auto" onclick="return validateform();" />
                    <input type="hidden" name="redirect_page_id" id="redirect_page_id" value="<?php if(isset($redirect_page_id)) echo esc_url(get_the_permalink($redirect_page_id)); ?>" >
                    <input type="hidden" name="to" value="<?php echo $to?>">
                    <input type="hidden" name="email_subject" value="<?php echo $email_subject?>">
                </div>
            </div>
        </div>

        <?php

            /*get page referer URL */

            if (isset($_SERVER['HTTP_REFERER'])) {
                $ref_url = $_SERVER['HTTP_REFERER']; //get referrer
            }else{
                $ref_url = 'Direct'; // show failure message
            }

            // session_start();

            // $external_url = $_SESSION['REFERER_ARRAY'][0];
            
            /* Get User Browser Name */

            function get_browser_name($user_agent)
            {
                if (strpos($user_agent, 'Opera') || strpos($user_agent, 'OPR/')) return 'Opera';
                elseif (strpos($user_agent, 'Edge')) return 'Edge';
                elseif (strpos($user_agent, 'Chrome')) return 'Chrome';
                elseif (strpos($user_agent, 'Safari')) return 'Safari';
                elseif (strpos($user_agent, 'Firefox')) return 'Firefox';
                elseif (strpos($user_agent, 'MSIE') || strpos($user_agent, 'Trident/7')) return 'Internet Explorer';
                
                return 'Other';
            }

            /* Get Form submitted date & time */

            $currentDateTime = date('d-m-Y');
            
        ?>

        <!-- User information -->

        <input id="contact_form_referral_URL" name="contact_form_referral_URL" type="hidden" value="" />
        <input id="contact_form_previous_URL" name="contact_form_previous_URL" type="hidden" value="<?php echo $ref_url; ?>" />
        <input id="contact_form_pagename" name="contact_form_pagename" type="hidden" value="<?php echo the_title();?>" />
        <input id="contact_form_name" name="contact_form_name" type="hidden" value="<?php echo "Get in Touch";?>" />
        <input id="contact_form_browsername" name="contact_form_browsername" type="hidden" value="<?php echo get_browser_name($_SERVER['HTTP_USER_AGENT']);?>" />
        <input id="contact_form_submitted_date" name="contact_form_submitted_date" type="hidden" value="<?php echo $currentDateTime;?>" />
        <input id="contact_form_timezone" name="contact_form_timezone" type="hidden" value="" />


	    <?php wp_nonce_field('contact_nonce_action','contact_nonce_field'); ?>
    </form>
</div>