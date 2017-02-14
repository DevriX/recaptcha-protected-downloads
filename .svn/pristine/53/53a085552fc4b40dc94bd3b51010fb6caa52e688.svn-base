<?php
/*
Plugin Name: Jetpack reCaptcha
Plugin URI: https://github.com/elhardoum/jetpack-recaptcha
Description: Google reCaptcha integration for Jetpack contact forms
Author: Samuel Elh
Version: 0.1
Author URI: https://samelh.com
Text Domain: jp-recaptcha
*/

// prevent direct access
defined('ABSPATH') || exit('Direct access not allowed.' . PHP_EOL);

class JPreCaptcha
{
    /** Class instance **/
    protected static $instance = null;

    /** Get Class instance **/
    public static function instance()
    {
        return null == self::$instance ? new self : self::$instance;
    }

    public static function init()
    {
        return self::instance()
            ->setupGlobals()
            ->setupConstants()
            ->loadTextDomain()
            ->mergeSettings()
            ->loadComponent();
    }

    public function setupGlobals()
    {
        global $JPreCaptcha, $JPreCaptchaCore;

        $JPreCaptcha = (object) array(
            'public' => null,
            'secret' => null,
            'locale' => 'en',
            'ready' => false,
            'locales' => array(
                "ar" => "Arabic",
                "af" => "Afrikaans",
                "am" => "Amharic",
                "hy" => "Armenian",
                "az" => "Azerbaijani",
                "eu" => "Basque",
                "bn" => "Bengali",
                "bg" => "Bulgarian",
                "ca" => "Catalan",
                "zh-HK" => "Chinese (Hong Kong)",
                "zh-CN" => "Chinese (Simplified)",
                "zh-TW" => "Chinese (Traditional)",
                "hr" => "Croatian",
                "cs" => "Czech",
                "da" => "Danish",
                "nl" => "Dutch",
                "en-GB" => "English (UK)",
                "en" => "English (US)",
                "et" => "Estonian",
                "fil" => "Filipino",
                "fi" => "Finnish",
                "fr" => "French",
                "fr-CA" => "French (Canadian)",
                "gl" => "Galician",
                "ka" => "Georgian",
                "de" => "German",
                "de-AT" => "German (Austria)",
                "de-CH" => "German (Switzerland)",
                "el" => "Greek",
                "gu" => "Gujarati",
                "iw" => "Hebrew",
                "hi" => "Hindi",
                "hu" => "Hungarain",
                "is" => "Icelandic",
                "id" => "Indonesian",
                "it" => "Italian",
                "ja" => "Japanese",
                "kn" => "Kannada",
                "ko" => "Korean",
                "lo" => "Laothian",
                "lv" => "Latvian",
                "lt" => "Lithuanian",
                "ms" => "Malay",
                "ml" => "Malayalam",
                "mr" => "Marathi",
                "mn" => "Mongolian",
                "no" => "Norwegian",
                "fa" => "Persian",
                "pl" => "Polish",
                "pt" => "Portuguese",
                "pt-BR" => "Portuguese (Brazil)",
                "pt-PT" => "Portuguese (Portugal)",
                "ro" => "Romanian",
                "ru" => "Russian",
                "sr" => "Serbian",
                "si" => "Sinhalese",
                "sk" => "Slovak",
                "sl" => "Slovenian",
                "es" => "Spanish",
                "es-419" => "Spanish (Latin America)",
                "sw" => "Swahili",
                "sv" => "Swedish",
                "ta" => "Tamil",
                "te" => "Telugu",
                "th" => "Thai",
                "tr" => "Turkish",
                "uk" => "Ukrainian",
                "ur" => "Urdu",
                "vi" => "Vietnamese",
                "zu" => "Zulu"
            )
        );

        $JPreCaptchaCore = self::instance(); 

        return $JPreCaptchaCore;
    }

    public static function setupConstants()
    {
        $ins = $GLOBALS['JPreCaptchaCore'];

        $constants = array(
            "JPRECAPTCHA_FILE" => __FILE__,
            "JPRECAPTCHA_DIR" => plugin_dir_path(__FILE__),
            "JPRECAPTCHA_BASE" => plugin_basename(__FILE__),
            "JPRECAPTCHA_DOMAIN" => 'jp-recaptcha'
        );

        foreach ( $constants as $constant => $def ) {
            if ( !defined( $constant ) ) {
                define( $constant, $def );
            }
        }

        return $ins;
    }

    public static function loadTextDomain()
    {
        $ins = $GLOBALS['JPreCaptchaCore'];
        
        load_plugin_textdomain(JPRECAPTCHA_DOMAIN, FALSE, dirname(JPRECAPTCHA_BASE).'/languages');

        return $ins;
    }

    public static function mergeSettings()
    {
        $ins = $GLOBALS['JPreCaptchaCore'];

        if ( $ins->isNetworkActive() ) {
            $settings = get_site_option('JPreCaptcha_settings', array());
        } else {
            $settings = get_option('JPreCaptcha_settings', array());
        }

        global $JPreCaptcha;
        $JPreCaptcha = (object) wp_parse_args($settings, (array) $JPreCaptcha);
        $JPreCaptcha->ready = $JPreCaptcha->public && $JPreCaptcha->secret;

        return $ins;
    }

    public static function loadComponent()
    {
        $ins = $GLOBALS['JPreCaptchaCore'];

        if ( is_admin() ) {
            $ins::adminInit();
        } else {

            global $JPreCaptcha;

            if ( isset($JPreCaptcha->ready) && $JPreCaptcha->ready ) {
                
                require_once JPRECAPTCHA_DIR . (
                    'Inc/Lib/recaptcha/src/autoload.php'
                );

                if ( !class_exists('\ReCaptcha\ReCaptcha') ) {
                    $JPreCaptcha->ready = false;
                    return;
                } else {
                    $JPreCaptcha->recaptcha = new \ReCaptcha\ReCaptcha($JPreCaptcha->secret);
                }

                add_filter('jetpack_contact_form_is_spam', array($ins, 'reCaptchaCheck'));
                add_filter('the_content', array($ins, 'appendField'));
                add_shortcode('jp-recaptcha', array($ins, 'shortcode'));
                add_action('wp_footer', array($ins, 'footerJS'));

                do_action('JPreCaptcha_ready', $JPreCaptcha);
            }
        }

        $GLOBALS['JPreCaptcha_core_instance'] = $ins;

        return $ins;
    }

    public static function adminInit()
    {
        $ins = $GLOBALS['JPreCaptchaCore'];

        require JPRECAPTCHA_DIR . (
            'Inc/Admin/JPreCaptchaAdmin.php'
        );

        if ( !class_exists('JPreCaptchaAdmin') )
            return $ins;

        $admin = JPreCaptchaAdmin::instance();

        if ( $ins->isNetworkActive() ) {
            add_action('network_admin_menu', array($admin, 'pages'));
        } else {
            add_action('admin_menu', array($admin, 'pages'));
        }

        if (isset($_GET['page']) && 'jp-recaptcha' === $_GET['page']) {
            global $pagenow;

            switch ($pagenow) {
                case 'settings.php':
                    if ( $ins->isNetworkActive() ) {
                        $admin->update();
                    }
                    break;

                case 'options-general.php':
                    if ( !$ins->isNetworkActive() ) {
                        $admin->update();
                    }
                    break;
            }
        }

        if ( $ins->isNetworkActive() ) {
            add_filter( 'network_admin_plugin_action_links_' . JPRECAPTCHA_BASE, array($admin, 'links'));
        } else {
            add_filter( 'plugin_action_links_' . JPRECAPTCHA_BASE, array($admin, 'links'));
        }

        return $ins;
    }

    public static function isNetworkActive() {
        if ( !is_multisite() )
            return false;

        $plugins = get_site_option( 'active_sitewide_plugins', array() );

        if ( !is_array($plugins) || !isset($plugins[JPRECAPTCHA_BASE]) ) {
            return false;
        }

        return true;
    }

    public static function reCaptchaCheck($bool)
    {
        global $JPreCaptcha;
        $success = false;

        if ( isset($_POST['g-recaptcha-response']) ) {
            $resp = $JPreCaptcha->recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);
            if ($resp->isSuccess()) {
                $success = true;
            }
        }
 
        if ( !$success && apply_filters('JPreCaptcha_fail', true, $bool) ) {
            $JPreCaptcha->failed = true;

            return new WP_Error(
                'possible_spam',
                __('Unable to process your submission, you have failed the spam test!', JPRECAPTCHA_DOMAIN)
            );
        }

        return $bool;
    }

    public static function appendField($c)
    {
        return preg_replace_callback(
            '/\[contact-form(.*?)?\](.*?)?\[\/contact-form\]/si',
            array($GLOBALS['JPreCaptchaCore'], 'appendFieldCallback'),
            $c
        );
    }

    public function appendFieldCallback($m)
    {
        $fields = isset($m[2]) ? $m[2] : null;
        $_fields = $fields . '[jp-recaptcha]';
        return str_replace($fields, $_fields, array_shift($m));
    }

    public static function shortcode($atts)
    {
        global $JPreCaptcha;

        ob_start();
        ?>
        <div class="jp-recaptcha-contain">
            <label for=""></label>
            <span class="g-recaptcha jp-recaptcha" data-sitekey="<?php echo $JPreCaptcha->public; ?>"></span>
            <?php if ( isset($JPreCaptcha->failed) && $JPreCaptcha->failed ) : ?>
                <p style="color: #dc3232" class="jp-recaptcha-message">
                    <?php _e('Please complete this test!', JPRECAPTCHA_DOMAIN); ?>
                </p>
            <?php endif; ?>
        </div>
        <?php

        return apply_filters('JPreCaptcha_field', ob_get_clean());
    }

    public static function footerJS()
    {
        global $JPreCaptcha;

        ?>
        <script type="text/javascript">
            if ( document.querySelector('.g-recaptcha.jp-recaptcha') ) {
                var appendJS = function(){
                    var js, b = document.body;
                    js = document.createElement('script');
                    js.type = 'text/javascript';
                    js.src = 'https://www.google.com/recaptcha/api.js?hl=<?php echo urlencode($JPreCaptcha->locale); ?>';
                    b.appendChild(js);
                }
                if(window.attachEvent) {
                    window.attachEvent('onload', appendJS);
                } else {
                    if(window.onload) {
                        var curronload = window.onload;
                        var newonload = function(evt) {
                            curronload(evt);
                            appendJS(evt);
                        };
                        window.onload = newonload;
                    } else {
                        window.onload = appendJS;
                    }
                }
            }
        </script>
        <?php
    }
}

add_action('plugins_loaded', array(JPreCaptcha::instance(), 'init'), 999);