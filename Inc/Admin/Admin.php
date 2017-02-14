<?php

// prevent direct access
defined('ABSPATH') || exit('Direct access not allowed.' . PHP_EOL);

class reCaptchaProtectedDownloadsAdmin
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
        global $reCaptchaProtectedDownloadsCore;
        $ins = self::instance();

        add_submenu_page(
            $reCaptchaProtectedDownloadsCore->isNetworkActive() ? 'settings.php' : 'options-general.php',
            __('reCaptcha Protected Downloads Settings', RCPDL_DOMAIN),
            __('reCaptcha Downloads', RCPDL_DOMAIN),
            'manage_options',
            'rcpdl',
            array($ins, "screen")
        );

        return $ins;
    }

    public static function screen()
    {
        global $reCaptchaProtectedDownloads;
        ?>

        <div class="wrap">
        
            <h2><?php _e('reCaptcha Protected Downloads Settings', RCPDL_DOMAIN); ?></h2>

            <?php if ( isset($reCaptchaProtectedDownloads->notices) ) : ?>
                <?php echo $reCaptchaProtectedDownloads->notices; ?>
            <?php endif; ?>

            <form method="post">

                <div class="section">
                    <p><strong><?php _e('reCaptcha credentials:', RCPDL_DOMAIN); ?></strong></p>

                    <p><?php _e('Before you setup this plugin, make sure to go to <a href="https://www.google.com/recaptcha" target="_blank">Google reCaptcha</a> website and register your site. After that, insert both public and secret captcha keys in the following fields. A <a href="https://www.google.com/search?q=how+to+get+google+recaptcha" target="_blank">tutorial</a> might also help.', RCPDL_DOMAIN); ?></p>

                    <p>
                        <label><?php _e('Enter your Google reCaptcha public key:', RCPDL_DOMAIN); ?><br/>
                        <input type="text" name="recaptcha_public" size="50" value="<?php echo esc_attr($reCaptchaProtectedDownloads->public); ?>" /></label>
                    </p>

                    <p>
                        <label><?php _e('Enter your Google reCaptcha secret key:', RCPDL_DOMAIN); ?><br/>
                        <input type="text" name="recaptcha_secret" size="50" value="<?php echo esc_attr($reCaptchaProtectedDownloads->secret); ?>" /></label>
                    </p>

                    <p>
                        <label for="recaptcha_locale"><strong><?php _e('reCaptcha Language:', RCPDL_DOMAIN); ?></strong></label>
                    </p>
                    <p>
                        <select name="recaptcha_locale" id="recaptcha_locale">
                            <?php foreach ( $reCaptchaProtectedDownloads->locales as $locale => $display ) : ?>
                                <option value="<?php echo esc_attr($locale); ?>" <?php selected($locale,$reCaptchaProtectedDownloads->locale); ?>><?php echo esc_attr($display); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </p>

                </div>

                <?php wp_nonce_field( 'reCaptchaProtectedDownloads_nonce', 'reCaptchaProtectedDownloads_nonce' ); ?>
                <?php submit_button(); ?>

            </form>
        
        </div>

        <?php
    }

    public static function update()
    {
        global $reCaptchaProtectedDownloads, $reCaptchaProtectedDownloadsCore;

        $settings = array();

        if ( isset($_POST['submit']) ) {
            if ( !isset($_POST['reCaptchaProtectedDownloads_nonce']) || !wp_verify_nonce($_POST['reCaptchaProtectedDownloads_nonce'], 'reCaptchaProtectedDownloads_nonce') ) {
                return;
            }

            if ( isset($_POST['recaptcha_public']) && trim($_POST['recaptcha_public']) ) {
                $settings['public'] = sanitize_text_field($_POST['recaptcha_public']);
            } else {
                $reCaptchaProtectedDownloads->public = ''; 
            }

            if ( isset($_POST['recaptcha_secret']) && trim($_POST['recaptcha_secret']) ) {
                $settings['secret'] = sanitize_text_field($_POST['recaptcha_secret']);
            } else {
                $reCaptchaProtectedDownloads->secret = '';
            }

            if ( isset($_POST['recaptcha_locale']) && isset($reCaptchaProtectedDownloads->locales[$_POST['recaptcha_locale']]) ) {
                $settings['locale'] = sanitize_text_field($_POST['recaptcha_locale']);
            } else {
                $reCaptchaProtectedDownloads->locale = 'en';
            }
        
            if ( $reCaptchaProtectedDownloadsCore->isNetworkActive() ) {
                update_site_option('reCaptchaProtectedDownloads_settings', $settings);
            } else {
                update_option('reCaptchaProtectedDownloads_settings', $settings);
            }

            // update global var
            $reCaptchaProtectedDownloadsCore->mergeSettings();

            if ( !isset($reCaptchaProtectedDownloads->notices) ) {
                $reCaptchaProtectedDownloads->notices = '';
            }

            // print feedback
            $reCaptchaProtectedDownloads->notices .= sprintf(
                '<div class="updated notice is-dismissible"><p>%s</p></div>',
                __('Settings updated successfully!', RCPDL_DOMAIN)
            );
        }
    }

    public static function links($links) {
        global $reCaptchaProtectedDownloadsCore;

        if ( $reCaptchaProtectedDownloadsCore->isNetworkActive() ) {
            $link = network_admin_url('settings.php?page=rcpdl');
        } else {
            $link = admin_url('options-general.php?page=rcpdl');
        }

        return array_merge(array(
            'Settings' => sprintf(
                '<a href="%s">' . __('Settings', RCPDL_DOMAIN) . '</a>',
                $link
            )
        ), $links);
    }
}