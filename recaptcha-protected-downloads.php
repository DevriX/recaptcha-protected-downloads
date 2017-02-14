<?php
/*
Plugin Name: reCaptcha Protected Downloads
Plugin URI: https://github.com/elhardoum/recaptcha-protected-downloads
Description: Protect your downloads from bots and spiders with a shortcode an Google's no-captcha reCaptcha
Author: Samuel Elh
Version: 0.1
Author URI: https://samelh.com
Text Domain: rcpdl
*/

// prevent direct access
defined('ABSPATH') || exit('Direct access not allowed.' . PHP_EOL);

class reCaptchaProtectedDownloads
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
        global $reCaptchaProtectedDownloads, $reCaptchaProtectedDownloadsCore;

        $reCaptchaProtectedDownloads = (object) array(
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

        $reCaptchaProtectedDownloadsCore = self::instance(); 

        return $reCaptchaProtectedDownloadsCore;
    }

    public static function setupConstants()
    {
        $ins = $GLOBALS['reCaptchaProtectedDownloadsCore'];

        $constants = array(
            "RCPDL_FILE" => __FILE__,
            "RCPDL_DIR" => plugin_dir_path(__FILE__),
            "RCPDL_URL" => plugin_dir_url(__FILE__),
            "RCPDL_BASE" => plugin_basename(__FILE__),
            "RCPDL_DOMAIN" => 'rcpdl'
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
        $ins = $GLOBALS['reCaptchaProtectedDownloadsCore'];
        
        load_plugin_textdomain(RCPDL_DOMAIN, FALSE, dirname(RCPDL_BASE).'/languages');

        return $ins;
    }

    public static function mergeSettings()
    {
        $ins = $GLOBALS['reCaptchaProtectedDownloadsCore'];

        if ( $ins->isNetworkActive() ) {
            $settings = get_site_option('reCaptchaProtectedDownloads_settings', array());
        } else {
            $settings = get_option('reCaptchaProtectedDownloads_settings', array());
        }

        global $reCaptchaProtectedDownloads;
        $reCaptchaProtectedDownloads = (object) wp_parse_args($settings, (array) $reCaptchaProtectedDownloads);
        $reCaptchaProtectedDownloads->ready = $reCaptchaProtectedDownloads->public && $reCaptchaProtectedDownloads->secret;

        return $ins;
    }

    public static function loadComponent()
    {
        $ins = $GLOBALS['reCaptchaProtectedDownloadsCore'];

        if ( is_admin() ) {
            $ins::adminInit();            
        }

        global $reCaptchaProtectedDownloads;

        if ( isset($reCaptchaProtectedDownloads->ready) && $reCaptchaProtectedDownloads->ready ) {
            
            require_once RCPDL_DIR . (
                'Inc/Lib/recaptcha/src/autoload.php'
            );

            if ( !class_exists('\ReCaptcha\ReCaptcha') ) {
                $reCaptchaProtectedDownloads->ready = false;
                return;
            } else {
                $reCaptchaProtectedDownloads->recaptcha = new \ReCaptcha\ReCaptcha($reCaptchaProtectedDownloads->secret);
            }

            add_shortcode('recaptcha-protected-download', array($ins, 'shortcode'));
            add_action('wp_footer', array($ins, 'footerJS'));
            add_action('wp_footer', array($ins, 'recaptchaField'));
            add_action('wp_ajax_rcpdl_verify', array($ins, 'ajax'));
            add_action('wp_ajax_nopriv_rcpdl_verify', array($ins, 'ajax'));

            do_action('reCaptchaProtectedDownloads_ready', $reCaptchaProtectedDownloads);
        }

        $GLOBALS['reCaptchaProtectedDownloads_core_instance'] = $ins;

        return $ins;
    }

    public static function adminInit()
    {
        $ins = $GLOBALS['reCaptchaProtectedDownloadsCore'];

        require RCPDL_DIR . (
            'Inc/Admin/Admin.php'
        );

        if ( !class_exists('reCaptchaProtectedDownloadsAdmin') )
            return $ins;

        $admin = reCaptchaProtectedDownloadsAdmin::instance();

        if ( $ins->isNetworkActive() ) {
            add_action('network_admin_menu', array($admin, 'pages'));
        } else {
            add_action('admin_menu', array($admin, 'pages'));
        }

        if (isset($_GET['page']) && 'rcpdl' === $_GET['page']) {
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
            add_filter( 'network_admin_plugin_action_links_' . RCPDL_BASE, array($admin, 'links'));
        } else {
            add_filter( 'plugin_action_links_' . RCPDL_BASE, array($admin, 'links'));
        }

        return $ins;
    }

    public static function isNetworkActive() {
        if ( !is_multisite() )
            return false;

        $plugins = get_site_option( 'active_sitewide_plugins', array() );

        if ( !is_array($plugins) || !isset($plugins[RCPDL_BASE]) ) {
            return false;
        }

        return true;
    }

    public static function shortcode($atts, $link='')
    {
        $hash = apply_filters('rcpdl_hash', md5($link), $link);

        // add option if not there
        add_option("rcpdl_{$hash}", esc_attr(esc_url( $link )));

        return apply_filters('rcpdl_link', "#rcpdl={$hash}", $link, $hash);
    }

    public static function footerJS()
    {
        global $reCaptchaProtectedDownloads, $wp_scripts;

        $jquerySrc = $wp_scripts->registered['jquery']->src;
        if ( !$jquerySrc ) {
            $jquerySrc = $wp_scripts->registered['jquery-core']->src;
        } 

        ?>
        <script type="text/javascript">
            window.RCPDL = {
                recaptcha: (
                    'https://www.google.com/recaptcha/api.js?hl=<?php echo urlencode($reCaptchaProtectedDownloads->locale); ?>'
                ),
                jquery: '<?php echo esc_url($jquerySrc); ?>',
                recaptchaHTML: '<span class="g-recaptcha jp-recaptcha" data-sitekey="<?php echo $reCaptchaProtectedDownloads->public; ?>"></span>',
                stylesheet: '<?php echo apply_filters('rcpdl_stylesheet', RCPDL_URL); ?>assets/css/style.css',
                listener: {
                    listen: function(hash, callback){
                        RCPDL.listener.intervals[hash] = setInterval(function(){
                            callback();
                        }, 500);
                    },
                    stop: function(hash){
                        if ( "undefined" !== RCPDL.listener.intervals[hash] ) {
                            if ( clearInterval(RCPDL.listener.intervals[hash]) );
                            RCPDL.listener.intervals.splice(hash, 1);
                        }
                    },
                    intervals: []
                },
                ajaxurl: '<?php echo esc_url( admin_url('admin-ajax.php') ); ?>',
                i18n: {
                    err_general: '<?php esc_attr_e('error occured, please try again!', RCPDL_DOMAIN); ?>',
                    loading: '<?php esc_attr_e('(Loading ..)', RCPDL_DOMAIN); ?>',
                }
            }
            if ( document.querySelector('a[href*="#rcpdl="]') ) {
                var appendJS = function(){
                    var js, b = document.body;
                    js = document.createElement('script');
                    js.type = 'text/javascript';
                    js.src = '<?php echo apply_filters('rcpdl_js', RCPDL_URL); ?>assets/js/rcpdl.js';
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

    public static function recaptchaField()
    {
        global $reCaptchaProtectedDownloads;

        ob_start();
        ?>
        <div style="display:none" id="rcpdl-recaptcha">
            {{recaptcha}}
        </div>
        <?php
        print apply_filters('rcpdl_recaptcha_field', ob_get_clean());
    }

    public static function ajax()
    {
        global $reCaptchaProtectedDownloads;

        if ( !isset($reCaptchaProtectedDownloads->ready) || !$reCaptchaProtectedDownloads->ready ) {
            return wp_die('0');
        }      

        $hash = isset($_REQUEST['hash']) ? $_REQUEST['hash'] : null;

        if ( !$hash ) {
            return wp_send_json(array(
                'success' => false,
                'message' => __('Error occured, missing or invalid download', RCPDL_DOMAIN)
            ));
        }

        $recaptcha = isset($_REQUEST['recaptcha']) ? $_REQUEST['recaptcha'] : null;

        if ( !$recaptcha ) {
            return wp_send_json(array(
                'success' => false,
                'message' => __('Error occured, missing or failed recaptcha test!', RCPDL_DOMAIN)
            ));
        }

        $resp = $reCaptchaProtectedDownloads->recaptcha->verify($recaptcha, $_SERVER['REMOTE_ADDR']);
        if ($resp->isSuccess() || apply_filters('reCaptchaProtectedDownloads_fail_pass', false)) {

            $download_link = apply_filters('rcpdl_get_link_from_hash', get_option("rcpdl_{$hash}"), $hash);

            if ( $download_link ) {
                do_action('rcpdl_ajax_success', $download_link, $hash);

                return wp_send_json(array(
                    'success' => true,
                    'message' => __('Download success!', RCPDL_DOMAIN),
                    'download_link' => $download_link
                ));
            } else {
                return wp_send_json(array(
                    'success' => false,
                    'message' => __('Error occured, could not find this download!', RCPDL_DOMAIN)
                ));
            }
        } else {
            return wp_send_json(array(
                'success' => false,
                'message' => __('Error occured, missing or failed recaptcha test!', RCPDL_DOMAIN)
            ));
        }
    }
}

add_action('plugins_loaded', array(reCaptchaProtectedDownloads::instance(), 'init'), 999);