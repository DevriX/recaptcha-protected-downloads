=== reCaptcha Protected Downloads ===
Contributors: elhardoum
Tags: content, shortcode, download, spam, anti-spam, link, media, file, captcha, google, recaptcha
Requires at least: 4.7.2
Tested up to: 4.7.2
Stable tag: 0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Author URI: http://samelh.com/
Donate link: http://samelh.com/

Protect your downloads from bots and spiders with a shortcode and Google's no-captcha reCaptcha

== Description ==

<p>Use the shortcode <code>[recaptcha-protected-download]</code> to wrap any direct download link that you'd like reCaptcha to process.</p>

<p><code>[recaptcha-protected-download]https://example.com/download.zip[/recaptcha-protected-download]</code></p>

<strong>Example</strong>

<p>Here's my <code>hello world</code> post HTML:</p>

<pre>
Hello! This page is dynamically generated with my plugin called Content Generator which you can download from &lt;a href="[recaptcha-protected-download]https://my-cdn.tld/static/content-generator.zip[/recaptcha-protected-download]"&gt;here&lt;/a&gt;.

You can also download WordPress following &lt;a href="[recaptcha-protected-download]https://wordpress.org/latest.zip[/recaptcha-protected-download]"&gt;this URL&lt;/a&gt;
</pre>

<p>Which is parsed as follows:</p>

<pre>
Hello! This page is dynamically generated with my plugin called Content Generator which you can download from &lt;a href="#rcpdl=371bce9996c0afc711648c2c4f3d5c97"&gt;here&lt;/a&gt;.

You can also download WordPress following &lt;a href="#rcpdl=7039e773658ed3c70d50e791ed4940fd"&gt;this URL&lt;/a&gt;
</pre>

<strong>About</strong>

<p>This plugin opts to generate fake links (anchors) controlled with JavaScript to open up a modal once clicked to opt users to complete the anti-spam test with <a href="https://github.com/google/recaptcha/">Google reCaptcha</a>.</p>

<p>Once a user successfully submits a test, an AJAX callback will then verify the response and parse the download.</p>

<p><code>md5</code> is used to hash the download links and fetch them later once a user submits a test.</p>

<p>You must provide your Google reCaptcha credentials which you can obtain from <a href="https://google.com/recaptcha/admin">this page</a></p>

Once you activate the plugin, you should now navigate to "Settings" > "reCaptcha Downlaods" (or "Options" > "reCaptcha Downlaods" for network activated plugin) and add your Google reCaptcha credentials (public and private keys) which you can obtain from https://www.google.com/recaptcha/admin

The development version of this plugin is hosted on Github, feel free to fork it, contribute and improve it, or start a new issue if you want to report something like an unusual bug. 

Here's the Github repo: https://github.com/elhardoum/recaptcha-protected-downloads

Thank you!

== Installation ==

1. Visit 'Plugins > Add New'
2. Search for 'reCaptcha Protected Downloads'
3. Activate reCaptcha Protected Downloads from your Plugins page. You will have to activate it for the whole network.

Once you activate the plugin, you should now navigate to "Settings" > "reCaptcha Downlaods" (or "Options" > "reCaptcha Downlaods" for network activated plugin) and add your Google reCaptcha credentials (public and private keys) which you can obtain from https://www.google.com/recaptcha/admin

== Screenshots ==

1. reCaptcha popup after clicking download link

== Changelog ==

= 0.1 =
* Initial stable release.