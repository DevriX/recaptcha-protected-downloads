# reCaptcha Protected Downloads

Protect your downloads from bots and spiders with a shortcode an Google's no-captcha reCaptcha

## Use

Use the shortcode `[recaptcha-protected-download]` to wrap any direct download link that you'd like reCaptcha to process.

`[recaptcha-protected-download]https://example.com/download.zip[/recaptcha-protected-download]`

## Example

Here's my `hello world` post HTML:

```html
Hello! This page is dynamically generated with my plugin called Content Generator which you can download from <a href="[recaptcha-protected-download]https://my-cdn.tld/static/content-generator.zip[/recaptcha-protected-download]">here</a>.

You can also download WordPress following <a href="[recaptcha-protected-download]https://wordpress.org/latest.zip[/recaptcha-protected-download]">this URL</a>
```

## About

This plugin opts to generate fake links (anchors) controlled with JavaScript to open up a modal once clicked to opt users to complete the anti-spam test with <a href="https://github.com/google/recaptcha/">Google reCaptcha</a>.

Once a user successfully submits a test, an AJAX callback will then verify the response and parse the download.

`md5` is used to hash the download links and fetch them later once a user submits a test.

You must provide your Google reCaptcha credentials which you can obtain from <a href="https://google.com/recaptcha/admin">this page</a>