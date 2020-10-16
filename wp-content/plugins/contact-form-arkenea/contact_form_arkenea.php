<?php
/*
Plugin Name: Arkenea Contact Form
Description: Contact Form, Use shortcode: [cfa_contact_form]
Author: Shailaja-Arkenea
Author URL: https://profiles.wordpress.org/shailajabhagat
Version: 3.0.0
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


add_action('admin_menu', 'add_cfa_menu');

function add_cfa_menu () {
    add_submenu_page('options-general.php', 'CFA', 'CFA', 'manage_options', 'cfa', 'cfa_menu_html');
}

add_action('admin_init', 'register_cfa_settings');

function register_cfa_settings() {
    register_setting('cfa_group', 'cfa_option');
}

function cfa_menu_html() {

    ?>
    <h1>Arkenea Contact Form</h1>

    <form method="post" action="options.php">
        <?php
            settings_fields('cfa_group');
            $options = get_option('cfa_option');
        ?>
        <!-- GOOGLE CAPTCHA BLOCK -->
		<div class="google-captcha section-block">
			<h1>Google Captcha</h1>
			<div class="section-block-inner">
				<table>
                    <tr>
                        <td colspan="2"><h2>Live Site</h2></td>
                    </tr>
					<tr>
						<th>Site Key</th>
						<td>
							<input class="go_sitekey" type="text" name="cfa_option[go][live_sitekey]" size="60" value="<?php echo $options['go']['live_sitekey']; ?>">
						</td>
					</tr>
					<tr>
						<th>Secret Key</th>
						<td>
							<input class="go_secretkey" type="text" name="cfa_option[go][live_secretkey]" size="60" value="<?php echo $options['go']['live_secretkey']; ?>">
						</td>
					</tr>
                    <tr>
                        <th></th>
                        <td><input type="submit" class="button-primary" value="Save"></td>
                    </tr>
                    <tr>
                        <td colspan="2"><h2>Local Site</h2></td>
                    </tr>
					<tr>
						<th>Site Key</th>
						<td>
							<input class="go_sitekey" type="text" name="cfa_option[go][local_sitekey]" size="60" value="<?php echo $options['go']['local_sitekey']; ?>">
						</td>
					</tr>
					<tr>
						<th>Secret Key</th>
						<td>
							<input class="go_secretkey" type="text" name="cfa_option[go][local_secretkey]" size="60" value="<?php echo $options['go']['local_secretkey']; ?>">
						</td>
					</tr>
						<tr>
						<th></th>
						<td><input type="submit" class="button-primary" value="Save"></td>
					</tr>
				</table>
                <div style="margin: 10px 0 0;text-align:right;font-size: 12px;">Note: Don't worry! Server will be auto detected and respected keys will be applied.</div>
			</div>
		</div>
		<!-- END GOOGLE CAPTCHA BLOCK -->

        <!-- Contact Form Details BLOCK -->
        <div class="google-captcha section-block">
            <h1>Contact Form</h1>
            <div class="section-block-inner">
                <table>
                    <tr>
                        <th>To Email</th>
                        <td>
                            <input type="text" name="cfa_option[form][email]" size="60" value="<?php echo $options['form']['email']; ?>">
                        </td>
                    </tr>
                    <tr>
                        <th>BCC Email</th>
                        <td>
                            <input type="text" name="cfa_option[form][bcc_first]" size="60" value="<?php echo $options['form']['bcc_first']; ?>">
                        </td>
                    </tr>
                    <tr>
                        <th>2nd BCC Email</th>
                        <td>
                            <input type="text" name="cfa_option[form][bcc_second]" size="60" value="<?php echo $options['form']['bcc_second']; ?>">
                        </td>
                    </tr>
                    <tr>
                        <th>Email Subject</th>
                        <td>
                            <input type="text" name="cfa_option[form][subject]" size="60" value="<?php echo $options['form']['subject']; ?>">
                        </td>
                    </tr>
                    <tr>
                        <th>Thank you Page</th>
                        <td>
                            <select name="cfa_option[form][thanks]">
                                <option value=""> -- Select Page -- </option>
                                <?php
                                $children = get_pages(
                                    array(
                                        'sort_column' => 'menu_order',
                                        'sort_order' => 'ASC',
                                        'hierarchical' => 0,
                                        'post_type' => 'page',
                                        'post_status' => 'publish'
                                    ));

                                foreach( $children as $post ) {
                                    if($options['form']['thanks'] == $post->ID) {
                                        echo "<option value='$post->ID' selected=selected>$post->post_title</option>";
                                    } else {
                                        echo "<option value='$post->ID'>$post->post_title</option>";
                                    }
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td><input type="submit" class="button-primary" value="Save"></td>
                    </tr>
                </table>
            </div>
        </div>
        <!-- END GOOGLE CAPTCHA BLOCK -->

    </form>
    <style>
        .info_span{
            font-size: 14px;margin-top: -5px;display: block;
        }
        .section-block input[type="text"], .section-block textarea, .section-block select {
            min-width: 500px;
            margin: 0 0 10px;
        }
        .section-block textarea {
            min-height: 100px;
        }
        .section-block table {
            border-collapse: collapse;
            width: 100%;
        }
        .section-block tr {
            border-bottom: 0px solid transparent;
        }
        .section-block td, .section-block th {
            padding-right: 30px;
        }
        .section-block td h2 {
            font-weight: 700;
            font-weight: 700;
            font-size: 16px;
            line-height: 20px;
            border-bottom: 1px solid;
            padding-bottom: 5px;
        }
        .section-block th {
            text-align: right;
            /*min-width: 130px;*/
            max-width: 50px;
            vertical-align: top;
            padding-top: 5px;
        }
        .section-block-inner{
            padding: 20px;
        }
        .section-block {
            background: rgb(223, 235, 239);
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
            width: 95%;
            margin: 30px 0;
            -webkit-box-shadow: 0 0 3px rgba(35, 40, 45, 0.5);
            -moz-box-shadow: 0 0 3px rgba(35, 40, 45, 0.5);
            box-shadow: 0 0 3px rgba(35, 40, 45, 0.5);
        }
        .section-block h1 {
            margin: 0 0 0;
            background: #23282d;
            color: #FFF;
            line-height: 50px;
            padding-left: 20px;
            text-transform: uppercase;
            font-size: 20px;
        }
        .section-block h3 {
            margin-top: 0;
        }
    </style>
<?php
}

function cfa_contact_form_shortcode($attr) {

	if( file_exists(plugin_dir_path( __FILE__ ).'/form.php' )) {
			ob_start();
			include(plugin_dir_path( __FILE__ ).'form.php');
			return ob_get_clean();
	}
}

// Register the shortcode to the function ec_shortcode()
add_shortcode( 'cfa_contact_form', 'cfa_contact_form_shortcode' );

add_action( 'wp_enqueue_scripts', 'cfa_ajax_contact_scripts' );
function cfa_ajax_contact_scripts() {
	//css
    wp_enqueue_style( 'cfa_style', plugins_url( '/css/contact_style.css', __FILE__ ));

    //js
    wp_enqueue_script( 'cfa_contact_form', plugins_url( '/js/contact_script.js', __FILE__ ), array('jquery'), '1.0', true );
    wp_localize_script( 'cfa_contact_form', 'contact', array(
        'ajax_url' => admin_url( 'admin-ajax.php' )
    ));
}

add_action( 'wp_ajax_nopriv_cfa_form_data_process', 'cfa_form_data_process' );
add_action( 'wp_ajax_cfa_form_data_process', 'cfa_form_data_process' );

//After submission process starts
function cfa_form_data_process() {

	ob_clean();

	$data = $_POST['form_data'];
	parse_str($_POST['form_data'], $data);

	if($data['contact_form_email']){
		if ( $data['contact_nonce_field'] == '' ){
			echo ('Security check fail.');
			wp_die();
		}
    }
    
    //define vars
	$website            = get_bloginfo( 'name' );

    $to			        = $data['to'] != ''  ? sanitize_email($data['to']) : sanitize_email(get_option('admin_email'));
	$redirect_page_id   = esc_url($data['redirect_page_id']);
	$email_subject      = sanitize_text_field($data['email_subject']);
	$email_bcc_first    = sanitize_text_field($data['email_bcc_first']);
	$email_bcc_second   = sanitize_text_field($data['email_bcc_second']);
	$type               = 'contact enquiry';
	
    $ip = "";
    $ip=$_SERVER['REMOTE_ADDR'];
    
    $referral_URL1 = $data["contact_form_referral_URL"];
    $datas = str_replace(" ",'',$data["contact_form_referral_URL"]);
    $datas = str_replace(",",' >> ',$datas);
    $datas = str_replace(">> >>",'>>',$datas);
    $datas = str_replace(">>",'line',$datas);

    $datas = explode('line',$datas);
    // echo "<pre>";print_r($datas);
    $htmlrefer ='<table style="width:100%">';
        foreach($datas as $row){
            if(!empty($row)){
                    $htmlrefer .= '<tr><td>'.$row.'</td></tr>';                       
            }
        }
    $htmlrefer .='</table>';               
    
    // echo $htmlrefer;
    $referral_URL2 = $htmlrefer;

    // sanitize form values
    $fname   = sanitize_text_field( $data["contact_form_fname"] );
    $lname   = sanitize_text_field( $data["contact_form_lname"] );
    $company_name   = sanitize_text_field( $data["contact_form_company"] );
    $email   = sanitize_email( $data["contact_form_email"] );
    // $phone   = intval( $data["contact_form_phone"] );
    $phone   = $data["contact_form_phone"];
    $enquiry = sanitize_text_field( $data["contact_form_enquiry"] );
    $form_name = sanitize_text_field( $data["contact_form_name"] );
    $page_title = sanitize_text_field( $data["contact_form_pagename"] );

    $referral_URL = sanitize_text_field( $data["contact_form_referral_URL"] );
    $previous_URL = sanitize_text_field( $data["contact_form_previous_URL"] );
    $browser_name = sanitize_text_field( $data["contact_form_browsername"] );
    $form_submitted_date = sanitize_text_field( $data["contact_form_submitted_date"] );
    $current_timezone = sanitize_text_field( $data["contact_form_timezone"] );
    
    $geo = json_decode(file_get_contents("http://extreme-ip-lookup.com/json/$ip"));
    
    $country = isset($geo->country) ? $geo->country : "";
    $state = isset($geo->region) ? $geo->region : "";
    $city = isset($geo->city) ? $geo->city : "";
    $ipType = isset($geo->ipType) ?  $geo->ipType : "";
    $businessName = isset($geo->businessName) ? $geo->businessName : "";
    $businessWebsite = isset($geo->businessWebsite) ? $geo->businessWebsite : "";

    

    $address = "";
    $address .= 'Your City is ' . $city;
    $address .= ', ';
    $address .= 'Your State is ' . $state;
    $address .= ', ';
    $address .= 'Your Country is ' . $country;

    $dt = new DateTime("now", new DateTimeZone($current_timezone));

    /*echo '<pre>';
    print_r($dt);
    exit;*/

    $date = $dt->format('m/d/Y, H:i:s');

    $formatted_date = strtotime($date);

    $timeInHours = date('H', $formatted_date);



    // Fetching country code

    $countryCodeArray = array(
        'ANDORRA'                                   =>'+376',
        'UNITED ARAB EMIRATES'                      =>'+971',
        'AFGHANISTAN'                               =>'+93',
        'ANTIGUA AND BARBUDA'                       =>'+1268',
        'ANGUILLA'                                  =>'+1264',
        'ALBANIA'                                   =>'+355',
        'ARMENIA'                                   =>'+374',
        'NETHERLANDS ANTILLES'                      =>'+599',
        'ANGOLA'                                    =>'+244',
        'ANTARCTICA'                                =>'+672',
        'ARGENTINA'                                 =>'+54',
        'AMERICAN SAMOA'                            =>'+1684',
        'AUSTRIA'                                   =>'+43',
        'AUSTRALIA'                                 =>'+61',
        'ARUBA'                                     =>'+297',
        'AZERBAIJAN'                                =>'+994',
        'BOSNIA AND HERZEGOVINA'                    =>'+387',
        'BARBADOS'                                  =>'+1246',
        'BANGLADESH'                                =>'+880',
        'BELGIUM'                                   =>'+32',
        'BURKINA FASO'                              =>'+226',
        'BULGARIA'                                  =>'+359',
        'BAHRAIN'                                   =>'+973',
        'BURUNDI'                                   =>'+257',
        'BENIN'                                     =>'+229',
        'SAINT BARTHELEMY'                          =>'+590',
        'BERMUDA'                                   =>'+1441',
        'BRUNEI DARUSSALAM'                         =>'+673',
        'BOLIVIA'                                   =>'+591',
        'BRAZIL'                                    =>'+55',
        'BAHAMAS'                                   =>'+1242',
        'BHUTAN'                                    =>'+975',
        'BOTSWANA'                                  =>'+267',
        'BELARUS'                                   =>'+375',
        'BELIZE'                                    =>'+501',
        'CANADA'                                    =>'+1',
        'COCOS (KEELING) ISLANDS'                   =>'+61',
        'CONGO, THE DEMOCRATIC REPUBLIC OF THE'     =>'+243',
        'CENTRAL AFRICAN REPUBLIC'                  =>'+236',
        'CONGO'                                     =>'+242',
        'SWITZERLAND'                               =>'+41',
        'COTE D IVOIRE'                             =>'+225',
        'COOK ISLANDS'                              =>'+682',
        'CHILE'                                     =>'+56',
        'CAMEROON'                                  =>'+237',
        'CHINA'                                     =>'+86',
        'COLOMBIA'                                  =>'+57',
        'COSTA RICA'                                =>'+506',
        'CUBA'                                      =>'+53',
        'CAPE VERDE'                                =>'+238',
        'CHRISTMAS ISLAND'                          =>'+61',
        'CYPRUS'                                    =>'+357',
        'CZECH REPUBLIC'                            =>'+420',
        'GERMANY'                                   =>'+49',
        'DJIBOUTI'                                  =>'+253',
        'DENMARK'                                   =>'+45',
        'DOMINICA'                                  =>'+1767',
        'DOMINICAN REPUBLIC'                        =>'+1809',
        'ALGERIA'                                   =>'+213',
        'ECUADOR'                                   =>'+593',
        'ESTONIA'                                   =>'+372',
        'EGYPT'                                     =>'+20',
        'ERITREA'                                   =>'+291',
        'SPAIN'                                     =>'+34',
        'ETHIOPIA'                                  =>'+251',
        'FINLAND'                                   =>'+358',
        'FIJI'                                      =>'+679',
        'FALKLAND ISLANDS (MALVINAS)'               =>'+500',
        'MICRONESIA, FEDERATED STATES OF'           =>'+691',
        'FAROE ISLANDS'                             =>'+298',
        'FRANCE'                                    =>'+33',
        'GABON'                                     =>'+241',
        'UNITED KINGDOM'                            =>'+44',
        'GRENADA'                                   =>'+1473',
        'GEORGIA'                                   =>'+995',
        'GHANA'                                     =>'+233',
        'GIBRALTAR'                                 =>'+350',
        'GREENLAND'                                 =>'+299',
        'GAMBIA'                                    =>'+220',
        'GUINEA'                                    =>'+224',
        'EQUATORIAL GUINEA'                         =>'+240',
        'GREECE'                                    =>'+30',
        'GUATEMALA'                                 =>'+502',
        'GUAM'                                      =>'+1671',
        'GUINEA-BISSAU'                             =>'+245',
        'GUYANA'                                    =>'+592',
        'HONG KONG'                                 =>'+852',
        'HONDURAS'                                  =>'+504',
        'CROATIA'                                   =>'+385',
        'HAITI'                                     =>'+509',
        'HUNGARY'                                   =>'+36',
        'INDONESIA'                                 =>'+62',
        'IRELAND'                                   =>'+353',
        'ISRAEL'                                    =>'+972',
        'ISLE OF MAN'                               =>'+44',
        'INDIA'                                     =>'+91',
        'IRAQ'                                      =>'+964',
        'IRAN, ISLAMIC REPUBLIC OF'                 =>'+98',
        'ICELAND'                                   =>'+354',
        'ITALY'                                     =>'+39',
        'JAMAICA'                                   =>'+1876',
        'JORDAN'                                    =>'+962',
        'JAPAN'                                     =>'+81',
        'KENYA'                                     =>'+254',
        'KYRGYZSTAN'                                =>'+996',
        'CAMBODIA'                                  =>'+855',
        'KIRIBATI'                                  =>'+686',
        'COMOROS'                                   =>'+269',
        'SAINT KITTS AND NEVIS'                     =>'+1869',
        'KOREA DEMOCRATIC PEOPLES REPUBLIC OF'      =>'+850',
        'KOREA REPUBLIC OF'                         =>'+82',
        'KUWAIT'                                    =>'+965',
        'CAYMAN ISLANDS'                            =>'+1345',
        'KAZAKSTAN'                                 =>'+7',
        'LAO PEOPLES DEMOCRATIC REPUBLIC'           =>'+856',
        'LEBANON'                                   =>'+961',
        'SAINT LUCIA'                               =>'+1758',
        'LIECHTENSTEIN'                             =>'+423',
        'SRI LANKA'                                 =>'+94',
        'LIBERIA'                                   =>'+231',
        'LESOTHO'                                   =>'+266',
        'LITHUANIA'                                 =>'+370',
        'LUXEMBOURG'                                =>'+352',
        'LATVIA'                                    =>'+371',
        'LIBYAN ARAB JAMAHIRIYA'                    =>'+218',
        'MOROCCO'                                   =>'+212',
        'MONACO'                                    =>'+377',
        'MOLDOVA, REPUBLIC OF'                      =>'+373',
        'MONTENEGRO'                                =>'+382',
        'SAINT MARTIN'                              =>'+1599',
        'MADAGASCAR'                                =>'+261',
        'MARSHALL ISLANDS'                          =>'+692',
        'MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF'=>'+389',
        'MALI'                                      =>'+223',
        'MYANMAR'                                   =>'+95',
        'MONGOLIA'                                  =>'+976',
        'MACAU'                                     =>'+853',
        'NORTHERN MARIANA ISLANDS'                  =>'+1670',
        'MAURITANIA'                                =>'+222',
        'MONTSERRAT'                                =>'+1664',
        'MALTA'                                     =>'+356',
        'MAURITIUS'                                 =>'+230',
        'MALDIVES'                                  =>'+960',
        'MALAWI'                                    =>'+265',
        'MEXICO'                                    =>'+52',
        'MALAYSIA'                                  =>'+60',
        'MOZAMBIQUE'                                =>'+258',
        'NAMIBIA'                                   =>'+264',
        'NEW CALEDONIA'                             =>'+687',
        'NIGER'                                     =>'+227',
        'NIGERIA'                                   =>'+234',
        'NICARAGUA'                                 =>'+505',
        'NETHERLANDS'                               =>'+31',
        'NORWAY'                                    =>'+47',
        'NEPAL'                                     =>'+977',
        'NAURU'                                     =>'+674',
        'NIUE'                                      =>'+683',
        'NEW ZEALAND'                               =>'+64',
        'OMAN'                                      =>'+968',
        'PANAMA'                                    =>'+507',
        'PERU'                                      =>'+51',
        'FRENCH POLYNESIA'                          =>'+689',
        'PAPUA NEW GUINEA'                          =>'+675',
        'PHILIPPINES'                               =>'+63',
        'PAKISTAN'                                  =>'+92',
        'POLAND'                                    =>'+48',
        'SAINT PIERRE AND MIQUELON'                 =>'+508',
        'PITCAIRN'                                  =>'+870',
        'PUERTO RICO'                               =>'+1',
        'PORTUGAL'                                  =>'+351',
        'PALAU'                                     =>'+680',
        'PARAGUAY'                                  =>'+595',
        'QATAR'                                     =>'+974',
        'ROMANIA'                                   =>'+40',
        'SERBIA'                                    =>'+381',
        'RUSSIAN FEDERATION'                        =>'+7',
        'RWANDA'                                    =>'+250',
        'SAUDI ARABIA'                              =>'+966',
        'SOLOMON ISLANDS'                           =>'+677',
        'SEYCHELLES'                                =>'+248',
        'SUDAN'                                     =>'+249',
        'SWEDEN'                                    =>'+46',
        'SINGAPORE'                                 =>'+65',
        'SAINT HELENA'                              =>'+290',
        'SLOVENIA'                                  =>'+386',
        'SLOVAKIA'                                  =>'+421',
        'SIERRA LEONE'                              =>'+232',
        'SAN MARINO'                                =>'+378',
        'SENEGAL'                                   =>'+221',
        'SOMALIA'                                   =>'+252',
        'SURINAME'                                  =>'+597',
        'SAO TOME AND PRINCIPE'                     =>'+239',
        'EL SALVADOR'                               =>'+503',
        'SYRIAN ARAB REPUBLIC'                      =>'+963',
        'SWAZILAND'                                 =>'+268',
        'TURKS AND CAICOS ISLANDS'                  =>'+1649',
        'CHAD'                                      =>'+235',
        'TOGO'                                      =>'+228',
        'THAILAND'                                  =>'+66',
        'TAJIKISTAN'                                =>'+992',
        'TOKELAU'                                   =>'+690',
        'TIMOR-LESTE'                               =>'+670',
        'TURKMENISTAN'                              =>'+993',
        'TUNISIA'                                   =>'+216',
        'TONGA'                                     =>'+676',
        'TURKEY'                                    =>'+90',
        'TRINIDAD AND TOBAGO'                       =>'+1868',
        'TUVALU'                                    =>'+688',
        'TAIWAN, PROVINCE OF CHINA'                 =>'+886',
        'TANZANIA, UNITED REPUBLIC OF'              =>'+255',
        'UKRAINE'                                   =>'+380',
        'UGANDA'                                    =>'+256',
        'UNITED STATES'                             =>'+1',
        'URUGUAY'                                   =>'+598',
        'UZBEKISTAN'                                =>'+998',
        'HOLY SEE (VATICAN CITY STATE)'             =>'+39',
        'SAINT VINCENT AND THE GRENADINES'          =>'+1784',
        'VENEZUELA'                                 =>'+58',
        'VIRGIN ISLANDS, BRITISH'                   =>'+1284',
        'VIRGIN ISLANDS, U.S.'                      =>'+1340',
        'VIET NAM'                                  =>'+84',
        'VANUATU'                                   =>'+678',
        'WALLIS AND FUTUNA'                         =>'+681',
        'SAMOA'                                     =>'+685',
        'KOSOVO'                                    =>'+381',
        'YEMEN'                                     =>'+967',
        'MAYOTTE'                                   =>'+262',
        'SOUTH AFRICA'                              =>'+27',
        'ZAMBIA'                                    =>'+260',
        'ZIMBABWE'                                  =>'+263'
    );
    
    if($country != ""){
        $phoneCountryCode = $countryCodeArray[strtoupper($country)].$phone;
    }
    else {
        $phoneCountryCode = "";
    }
    

    /* Store form data to DB */



    $formSubmittedDate = date("Y-m-d",strtotime($form_submitted_date));

    global $wpdb;
    $tableName = $wpdb->prefix . 'ark_contact_form';
    $data = array('name' => $fname, 'email' => $email, 'company' => $company_name, 'tel' => $phoneCountryCode, 'message' => $enquiry, 'form_name' => $form_name, 'page_name' => $page_title, 'referer_url' => $referral_URL1,'previous_url' => $previous_URL, 'submited_date' => $formSubmittedDate, 'ip' => $ip, 'browser_name' => $browser_name, 'users_address' => $address, 'form_submitted_time' => $date);
    $wpdb->insert($tableName,$data);
    $cfa_id = $wpdb->insert_id;

    //Email Body Starts
    $body	=	"Hi $website<br><br>";

    $body	.=	"The following $type was submitted via the $website website. <br><br>";

    $body	.=	"<span>First Name</span>:	".$fname. " <br>";
    $body	.=	"<span>Last Name</span>:	".$lname. " <br>";
    $body	.=	"<span>Company/Project</span>:	".$company_name. " <br>";
    $body	.=	"<span>Email</span>:	".$email. "<br>";

    if($phone)
        $body	.=	"<span>Phone</span>:	".$phoneCountryCode. "<br>";

    if($enquiry)
        $body	.=	"<span>Message</span>:	".$enquiry. "<br>";
    
    $body	.=	"<span>Form Name</span>:	".$form_name. "<br>";

    $body	.=	"<span>Page URL</span>:	".$page_title. "<br>";

    $body	.=	"<span>Referral URL</span>:	".$referral_URL2. "<br>";

    $body	.=	"<span>Previous page URL</span>:	".$previous_URL. "<br>";

    $body	.=	"<span>Address</span>:	".$address. "<br>";

    $body	.=	"<span>User ip</span>:	".$ip. "<br>";

    $body	.=	"<span>Form Submited Date</span>:	".$form_submitted_date. "<br>";
    
    $body	.=	"<br>--
                Thanks
                This e-mail was sent from a contact form on ".$page_title." (https://arkenea.com)<br><br>";

    $body	.=	"
            <style>
                body{
                    font-size:12px;font-family: Arial;
                }
                span{
                    width:80px;
                }
                </style>
            ";

    $subject	=	"A $website $type was received via the website";
    $subject    = isset($email_subject) ? $email_subject : $subject;

    $headers[]  = "From: $fname <$email>" . "\r\n";
    $headers[]  = "Content-type: text/html\r\n";
    $headers[]  = "Reply-To: $fname <$email>\r\n";
    $headers[]  = "Bcc: <$email_bcc_first>, <$email_bcc_second>";
    
    // If email has been process for sending, display a success message
    if ( wp_mail( $to, $subject, $body, $headers ) ) {

        if(!$redirect_page_id) {
            echo '<div style="color:green;"><strong>Thank you.</strong></div>';
            echo "<script>jQuery('#cfa_contact_form').fadeOut(500);</script>";
        } else {
            echo 'redirect_please';
            @file_put_contents(getcwd().'/userLogs/userlog'.date('Y-m-d').'.txt',"\n[===".date("d-m-Y H:i:s")."===\n] Users Info".json_encode($body)."\n\n", FILE_APPEND);
        }

    } else {
        echo 'An unexpected error occurred';
        echo "<script>jQuery('#contact_form_submit').removeClass('loading');</script>";
        echo "<script>jQuery('.contact_form_div').removeClass('loading_container');</script>";
    }
    
    //$countryCode = $countryCodeArray[strtoupper($country )];

    if($country != ""){
        $countryCode = $countryCodeArray[strtoupper($country)].$phone;
    }
    else {
        $countryCode = "";
    }

	wp_die();

}
?>