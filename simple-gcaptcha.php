<?php
/**
 * Plugin Name: Simple Gcaptcha
 * Description: Adds Google reCAPTCHA v3 to the WordPress login page.
 * Version: 1.0
 * Author: Dilhan Chandrasiri
 * License: GPL2
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . 'admin-settings.php';

function sgc_enqueue_recaptcha_script() {
    $site_key = get_option('sgc_recaptcha_site_key');
    if ($site_key) {
        echo '<script src="https://www.google.com/recaptcha/api.js?render=' . esc_attr($site_key) . '"></script>';
        echo '<script>
            grecaptcha.ready(function() {
                grecaptcha.execute("' . esc_attr($site_key) . '", {action: "login"}).then(function(token) {
                    document.getElementById("recaptcha_token").value = token;
                });
            });
        </script>';
    }
}

add_action('login_enqueue_scripts', 'sgc_enqueue_recaptcha_script');

function sgc_display_recaptcha_field() {
    echo '<input type="hidden" name="recaptcha_token" id="recaptcha_token">';
}

add_action('login_form', 'sgc_display_recaptcha_field');

function sgc_verify_recaptcha_v3($user, $password) {
    
    if (isset($_POST['recaptcha_token'])) {
        $recaptcha_token = sanitize_text_field($_POST['recaptcha_token']);
        $secret_key = get_option('sgc_recaptcha_secret_key');

        $response = wp_remote_post("https://www.google.com/recaptcha/api/siteverify", array(
            'body' => array(
                'secret' => $secret_key,
                'response' => $recaptcha_token,
                'remoteip' => $_SERVER['REMOTE_ADDR']
            )
        ));

        $response_body = wp_remote_retrieve_body($response);

        $result = json_decode($response_body);

        if (!$result->success || $result->score < 0.5) {
            return new WP_Error('captcha_error', __('<strong>ERROR</strong>: reCAPTCHA verification failed.'));
        }
    } else {
        return new WP_Error('captcha_error', __('<strong>ERROR</strong>: CAPTCHA is required.'));
    }

    return $user;
}

add_filter('wp_authenticate_user', 'sgc_verify_recaptcha_v3', 10, 2);

function sgc_activate_plugin() {

    add_option('sgc_plugin_activated', true);
}

register_activation_hook(__FILE__, 'sgc_activate_plugin');

function sgc_redirect_after_activation() {
    
    if (get_option('sgc_plugin_activated', false)) {
        
        delete_option('sgc_plugin_activated');
        
        
        wp_redirect(admin_url('options-general.php?page=sgc_settings'));
        exit;
    }
}

add_action('admin_init', 'sgc_redirect_after_activation');
