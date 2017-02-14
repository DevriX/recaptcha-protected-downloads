<?php

// prevent direct access
defined('ABSPATH') || exit('Direct access not allowed.' . PHP_EOL);

// if uninstall.php is not called by WordPress, die
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die('Direct access not allowed.' . PHP_EOL);
}

// delete settings
delete_site_option('reCaptchaProtectedDownloads_settings');

global $wpdb;

// delete hashed links
$wpdb->query(
    "DELETE FROM {$wpdb->options} WHERE `option_name` like 'rcpdl_%'"
);