<?php

// prevent direct access
defined('ABSPATH') || exit('Direct access not allowed.' . PHP_EOL);

class JPreCaptchaAdmin
{
    /** Class instance **/
    protected static $instance = null;

    /** Get Class instance **/
    public static function instance()
    {
        return null == self::$instance ? new self : self::$instance;
    }

    public static function pages()
    {
        global $JPreCaptchaCore;
        $ins = self::instance();

        add_submenu_page(
            $JPreCaptchaCore->isNetworkActive() ? 'settings.php' : 'options-general.php',
            __('JetPack reCaptcha Settings', JPRECAPTCHA_DOMAIN),
            __('JP reCaptcha', JPRECAPTCHA_DOMAIN),
            'manage_options',
            'jp-recaptcha',
            array($ins, "screen")
        );

        return $ins;
    }

    public static function screen()
    {
        global $JPreCaptcha;
        ?>

        <div class="wrap">
        
            <h2><?php _e('JetPack reCaptcha Settings', JPRECAPTCHA_DOMAIN); ?></h2>

            <?php if ( isset($JPreCaptcha->notices) ) : ?>
                <?php echo $JPreCaptcha->notices; ?>
            <?php endif; ?>

            <form method="post">

                <div class="section">
                    <p><strong><?php _e('reCaptcha credentials:', JPRECAPTCHA_DOMAIN); ?></strong></p>

                    <p><?php _e('Before you setup this plugin, make sure to go to <a href="https://www.google.com/recaptcha" target="_blank">Google reCaptcha</a> website and register your site. After that, insert both public and secret captcha keys in the following fields. A <a href="https://www.google.com/search?q=how+to+get+google+recaptcha" target="_blank">tutorial</a> might also help.', JPRECAPTCHA_DOMAIN); ?></p>

                    <p>
                        <label><?php _e('Enter your Google reCaptcha public key:', JPRECAPTCHA_DOMAIN); ?><br/>
                        <input type="text" name="recaptcha_public" size="50" value="<?php echo esc_attr($JPreCaptcha->public); ?>" /></label>
                    </p>

                    <p>
                        <label><?php _e('Enter your Google reCaptcha secret key:', JPRECAPTCHA_DOMAIN); ?><br/>
                        <input type="text" name="recaptcha_secret" size="50" value="<?php echo esc_attr($JPreCaptcha->secret); ?>" /></label>
                    </p>

                    <p>
                        <label for="recaptcha_locale"><strong><?php _e('reCaptcha Language:', JPRECAPTCHA_DOMAIN); ?></strong></label>
                    </p>
                    <p>
                        <select name="recaptcha_locale" id="recaptcha_locale">
                            <?php foreach ( $JPreCaptcha->locales as $locale => $display ) : ?>
                                <option value="<?php echo esc_attr($locale); ?>" <?php selected($locale,$JPreCaptcha->locale); ?>><?php echo esc_attr($display); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </p>

                </div>

                <?php wp_nonce_field( 'JPreCaptcha_nonce', 'JPreCaptcha_nonce' ); ?>
                <?php submit_button(); ?>

            </form>
        
        </div>

        <?php
    }

    public static function update()
    {
        global $JPreCaptcha, $JPreCaptchaCore;

        $settings = array();

        if ( isset($_POST['submit']) ) {
            if ( !isset($_POST['JPreCaptcha_nonce']) || !wp_verify_nonce($_POST['JPreCaptcha_nonce'], 'JPreCaptcha_nonce') ) {
                return;
            }

            if ( isset($_POST['recaptcha_public']) && trim($_POST['recaptcha_public']) ) {
                $settings['public'] = sanitize_text_field($_POST['recaptcha_public']);
            } else {
                $JPreCaptcha->public = ''; 
            }

            if ( isset($_POST['recaptcha_secret']) && trim($_POST['recaptcha_secret']) ) {
                $settings['secret'] = sanitize_text_field($_POST['recaptcha_secret']);
            } else {
                $JPreCaptcha->secret = '';
            }

            if ( isset($_POST['recaptcha_locale']) && isset($JPreCaptcha->locales[$_POST['recaptcha_locale']]) ) {
                $settings['locale'] = sanitize_text_field($_POST['recaptcha_locale']);
            } else {
                $JPreCaptcha->locale = 'en';
            }
        
            if ( $JPreCaptchaCore->isNetworkActive() ) {
                update_site_option('JPreCaptcha_settings', $settings);
            } else {
                update_option('JPreCaptcha_settings', $settings);
            }

            // update global var
            $JPreCaptchaCore->mergeSettings();

            if ( !isset($JPreCaptcha->notices) ) {
                $JPreCaptcha->notices = '';
            }

            // print feedback
            $JPreCaptcha->notices .= sprintf(
                '<div class="updated notice is-dismissible"><p>%s</p></div>',
                __('Settings updated successfully!', JPRECAPTCHA_DOMAIN)
            );
        }
    }

    public static function links($links) {
        global $JPreCaptchaCore;

        if ( $JPreCaptchaCore->isNetworkActive() ) {
            $link = network_admin_url('settings.php?page=jp-recaptcha');
        } else {
            $link = admin_url('options-general.php?page=jp-recaptcha');
        }

        return array_merge(array(
            'Settings' => sprintf(
                '<a href="%s">' . __('Settings', JPRECAPTCHA_DOMAIN) . '</a>',
                $link
            )
        ), $links);
    }
}