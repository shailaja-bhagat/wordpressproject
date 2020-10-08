=== Contact Form with Ajax ===
Contributors: faisal03
Donate link:
Tags: contact, form, ajax, enquiry, google-captcha
Requires at least: 3.3
Tested up to: 5.0
Stable tag: 4.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Create your form using a shortcode with the Google Captcha! Shortcode: [cfa_contact_form]

== Description ==

This is a simple plugin to generate a contact form using a shortcode.

<h4>The main feature are:</h4>
<ul>
    <li> Google Re Captcha.</li>
    <li> Can add All data from back end like: To Email, Email subject, Thank you Page, etc.</li>
    <li> AJAX is included so form will be fade out and display Thank you message <strong>without page load</strong> only if 'Thank you Page' id is not selected .</li>
    <li> If To Email id not selected in settings so data will be sent to admin email automatically.</li>
</ul>

Following fields are included (we will give access to add/update/delete fields in our next update soon):
<ul>
    <li>First Name</li>
    <li>Last Name</li>
    <li>Email Address</li>
    <li>Phone</li>
    <li>Message</li>
    <li>Google Captcha</li>
</ul>

<h4>How to use Shortcode:</h4>

Just use : **[cfa_contact_form]**<br>
This will use AJAX and data will be sent to admin email id. (Remember to add captcha keys in settings.)

<h4>Remember:</h4>
You must have to add Google Re Captcha keys. Please go to Settings > CFA.

<ul>
    <li>site key</li>
    <li>secret key</li>
</ul>

To create your website keys go to: https://www.google.com/recaptcha/intro/index.html.

== Installation ==

1. Upload plugin's zip file to the 'Add New Plugin' section in the WordPress.
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Place shortcode where you want to display shortcode.

== Frequently asked questions ==
= How to use plugin =

Plugin is used using [cfa_contact_form] shortcode

== Screenshots ==
1. Contact Form Layout
2. Form Fade Out - Thank you Message Displayed
3. Back End Support

== Changelog ==
= 3.0.0 =
Compatible with Gutenberg Sites with few minor fixes.

= 2.0.0 =
* Shortcode is changed from [contact_form] to [cfa_contact_form] to prevent conflicts.
* Google Captcha is added to the form.
* Back end interface added. Go to Settings > CFA.

= 1.0.0 =
* Includes shortcode [contact_form]

== Upgrade notice ==

= 3.0.0 =
Compatible with Gutenberg Sites with few minor fixes.

= 2.0.0 =
Everyone needs more security. So here is the Google Captcha added integrated in your form!