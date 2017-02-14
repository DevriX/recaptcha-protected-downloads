=== Jetpack reCaptcha ===
Contributors: elhardoum
Tags: multisite, forms, contact, jetpack, spam, anti-spam, captcha, google, recaptcha
Requires at least: 4.7
Tested up to: 4.7
Stable tag: 0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Author URI: http://samelh.com/
Donate link: http://samelh.com/

Google reCaptcha integration for Jetpack contact forms

== Description ==

Jetpack reCaptcha plugin helps integrate Google reCaptcha in your JetPack contact forms.

This plugin opts to filter your content and search for `[contact-form]` shortcode (i.e find the inserted contact forms) and append a new field to these forms, for parsing the Google reCaptcha `[jp-recaptcha]`.

If you're adding the contact form shortcode somewhere where the plugin cannot filter with `the_content` filter, please either add the shortcode `[jp-recaptcha]` where you want the reCaptcha field to be parsed, or better off, just point this plugin to filter your content with the following code:

<pre>
// get the global instance of JPreCaptcha class
global $JPreCaptchaCore;

// if your content is pluggable with a filter
add_filter('my_content_filter_tag_name', array($JPreCaptchaCore, 'appendField'));

// or, if not, just call the method directly
echo $JPreCaptchaCore::appendField( $myHTML );
</pre>

Once you activate the plugin, you should now navigate to "Settings" > "JP reCaptcha" (or "Options" > "JP reCaptcha" for network activated plugin) and add your Google reCaptcha credentials (public and private keys) which you can obtain from https://www.google.com/recaptcha/admin

The development version of this plugin is hosted on Github, feel free to fork it, contribute and improve it, or start a new issue if you want to report something like an unusual bug. 

Here's the Github repo: https://github.com/elhardoum/jetpack-recaptcha

Thank you!

== Installation ==

1. Visit 'Plugins > Add New'
2. Search for 'JetPack reCaptcha'
3. Activate JetPack reCaptcha from your Plugins page. You will have to activate it for the whole network.

Once you activate the plugin, you should now navigate to "Settings" > "JP reCaptcha" (or "Options" > "JP reCaptcha" for network activated plugin) and add your Google reCaptcha credentials (public and private keys) which you can obtain from https://www.google.com/recaptcha/admin

== Screenshots ==

1. Example form with reCaptcha field and a failed submission
2. Admin settings screen

== Changelog ==

= 0.1 =
* Initial stable release.