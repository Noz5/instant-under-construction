<?php
/*
Plugin Name: Instant Under Construction
Plugin URI: https://www.oakmonts.com.au/instant-under-construction
Description: Instant Under Construction is the ultimate no-nonsense, hassle-free, and completely free coming soon plugin for WordPress. With just one click, you can activate the coming soon mode and effortlessly create an engaging teaser page for your website. Say goodbye to complicated settings and convoluted configurations – our plugin allows you to enter your custom HTML code in seconds, providing a simple and straightforward solution for keeping your website under wraps until it's ready for its grand debut. Get your site up and running quickly and efficiently with Instant Under Construction!
Version: 1.0
Author: Ninos Hozaya
Author URI: https://www.oakmonts.com.au/
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

// Initialize the plugin
add_action('init', 'coming_soon_plugin_init');
function coming_soon_plugin_init() {
    // Check if the coming soon mode is enabled and the user is not an administrator
    if (get_option('coming_soon_plugin_enabled', false) && !current_user_can('manage_options')) {
        add_action('wp', 'coming_soon_plugin_display_page');
    }
}

// Create the coming soon page display function
function coming_soon_plugin_display_page() {
    $coming_soon_content = get_option('coming_soon_plugin_html', '');

    if (empty($coming_soon_content)) {
        $coming_soon_content = '<h1>Coming Soon</h1><p>Our website is under construction. Stay tuned!</p>';
    }

    // Sanitize the custom content before displaying it
    $coming_soon_content = wp_kses_post($coming_soon_content);

    wp_die($coming_soon_content, 'Coming Soon', array('response' => 503));
}

// Create the plugin settings page in the dashboard
add_action('admin_menu', 'coming_soon_plugin_settings_page');
function coming_soon_plugin_settings_page() {
    add_menu_page('Coming Soon', 'Coming Soon', 'manage_options', 'coming-soon-settings', 'coming_soon_plugin_settings_page_callback');
}

function coming_soon_plugin_settings_page_callback() {
    if (!current_user_can('manage_options')) {
        return;
    }

    // Verify the security nonce
    if (isset($_POST['coming_soon_settings_nonce']) && wp_verify_nonce($_POST['coming_soon_settings_nonce'], 'coming_soon_settings_nonce')) {
        // Save the HTML content if the form is submitted
        if (isset($_POST['coming_soon_content'])) {
            update_option('coming_soon_plugin_html', wp_unslash($_POST['coming_soon_content']));
        }
    }

    $coming_soon_content = get_option('coming_soon_plugin_html', '');
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form method="post" action="">
            <?php wp_nonce_field('coming_soon_settings_nonce', 'coming_soon_settings_nonce'); ?>
            <label for="coming_soon_content">Enter custom HTML content for the coming soon page:</label><br>
            <textarea name="coming_soon_content" id="coming_soon_content" cols="60" rows="10"><?php echo esc_textarea($coming_soon_content); ?></textarea><br>
            <input type="submit" value="Save" class="button button-primary">
        </form>
    </div>
    <?php
}

// Activation and Deactivation Hooks
register_activation_hook(__FILE__, 'coming_soon_plugin_activate');
function coming_soon_plugin_activate() {
    add_option('coming_soon_plugin_enabled', true);
    add_option('coming_soon_plugin_html', '');
}

register_deactivation_hook(__FILE__, 'coming_soon_plugin_deactivate');
function coming_soon_plugin_deactivate() {
    delete_option('coming_soon_plugin_enabled');
    delete_option('coming_soon_plugin_html');
}
?>
