<?php
function sgc_add_admin_menu() {
    add_options_page(
        'Simple Gcaptcha Settings',
        'Simple Gcaptcha',
        'manage_options',
        'sgc_settings',
        'sgc_settings_page'
    );
}
add_action('admin_menu', 'sgc_add_admin_menu');

function sgc_register_settings() {
    register_setting('sgc_settings_group', 'sgc_recaptcha_site_key');
    register_setting('sgc_settings_group', 'sgc_recaptcha_secret_key');
}
add_action('admin_init', 'sgc_register_settings');

function sgc_settings_page() {
    ?>
    <div class="wrap">
        <h1>Simple Gcaptcha Settings</h1>
        <form method="post" action="options.php">
            <?php settings_fields('sgc_settings_group'); ?>
            <?php do_settings_sections('sgc_settings_group'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Google reCAPTCHA Site Key</th>
                    <td><input type="text" name="sgc_recaptcha_site_key" value="<?php echo esc_attr(get_option('sgc_recaptcha_site_key')); ?>" /></td>
                </tr>
                 
                <tr valign="top">
                    <th scope="row">Google reCAPTCHA Secret Key</th>
                    <td><input type="text" name="sgc_recaptcha_secret_key" value="<?php echo esc_attr(get_option('sgc_recaptcha_secret_key')); ?>" /></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}
