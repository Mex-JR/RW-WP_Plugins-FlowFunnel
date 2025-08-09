<?php

/**
 * Plugin Name: Chat Widget for flowfunnel.io
 * Plugin URI:  http://riverworksit.com/Chat_Widget_for_FlowFunnel
 * Description: Customizable floating chat widget icon for your entire website with options
 * Version:     1.0
 * Author:      RiverWork IT LLC
 * Author URI:  http://riverworksit.com
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: chat-widget-flowfunnel
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) exit;

// Define plugin version constant
define('CHATWIDGETFLOWFUNNEL_VERSION', '1.0');

// Load Assets
function chatwidgetflowfunnel_enqueue_assets()
{
    $font_awesome_path = plugin_dir_path(__FILE__) . 'assets/css/all.min.css';
    $font_awesome_url = plugin_dir_url(__FILE__) . 'assets/css/all.min.css';

    // Always use local Font Awesome
    wp_register_style(
        'chatwidgetflowfunnel-font-awesome',
        $font_awesome_url,
        array(),
        '6.4.0',
        'all'
    );
    wp_enqueue_style('chatwidgetflowfunnel-font-awesome');

    wp_enqueue_style(
        'chatwidgetflowfunnel-tailwind-style',
        plugin_dir_url(__FILE__) . 'assets/css/style.css',
        array('chatwidgetflowfunnel-font-awesome'),
        filemtime(plugin_dir_path(__FILE__) . 'assets/css/style.css')
    );

    wp_enqueue_style(
        'chatwidgetflowfunnel-style',
        plugin_dir_url(__FILE__) . 'assets/css/frontend.css',
        array('chatwidgetflowfunnel-font-awesome', 'chatwidgetflowfunnel-tailwind-style'),
        filemtime(plugin_dir_path(__FILE__) . 'assets/css/frontend.css')
    );

    wp_enqueue_script(
        'chatwidgetflowfunnel-script',
        plugin_dir_url(__FILE__) . 'assets/js/script.js',
        array('jquery'),
        filemtime(plugin_dir_path(__FILE__) . 'assets/js/script.js'),
        true
    );

    wp_localize_script('chatwidgetflowfunnel-script', 'chatwidgetflowfunnelData', array(
        'phoneNumber' => get_option('chatwidgetflowfunnel_chat_number', ''),
        'countryCode' => get_option('chatwidgetflowfunnel_country_code', '+1'),
        'inquiryOptions' => get_option('chatwidgetflowfunnel_chat_options', array()),
        'trackingEnabled' => get_option('chatwidgetflowfunnel_chat_tracking', 'no'),
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('easy_chat_track_nonce')
    ));
}
add_action('wp_enqueue_scripts', 'chatwidgetflowfunnel_enqueue_assets');

// Load Admin Assets
function chatwidgetflowfunnel_enqueue_admin_assets($hook)
{
    if ($hook !== 'settings_page_chat-widget-flowfunnel-settings') {
        return;
    }
    $font_awesome_url = plugin_dir_url(__FILE__) . 'assets/css/all.min.css';
    wp_enqueue_style(
        'chatwidgetflowfunnel-font-awesome',
        $font_awesome_url,
        array(),
        'all'
    );
    wp_enqueue_style(
        'chatwidgetflowfunnel-tailwind-style',
        plugin_dir_url(__FILE__) . 'assets/css/style.css',
        array('chatwidgetflowfunnel-font-awesome'),
        filemtime(plugin_dir_path(__FILE__) . 'assets/css/style.css')
    );
}
add_action('admin_enqueue_scripts', 'chatwidgetflowfunnel_enqueue_admin_assets');

// Include Admin Settings & Analytics
include_once plugin_dir_path(__FILE__) . 'includes/admin-settings.php';
include_once plugin_dir_path(__FILE__) . 'includes/analytics.php';
require_once plugin_dir_path(__FILE__) . 'includes/ajax-handlers.php';

// Display Chat Widget Button
function chatwidgetflowfunnel_button()
{
    $number = get_option('chatwidgetflowfunnel_chat_number');
    $country_code = get_option('chatwidgetflowfunnel_country_code', '+1');
    $full_number = preg_replace('/[^0-9]/', '', $country_code . $number);
    if (!empty($number)) {
        $position = get_option('chatwidgetflowfunnel_chat_position', 'bottom-right');
        $icon_style = get_option('chatwidgetflowfunnel_chat_icon_style', 'style1');
        $inquiry_options = get_option('chatwidgetflowfunnel_chat_options', array());
?>
        <div class="chatwidgetflowfunnel-container <?php echo esc_attr($position); ?>">
            <div class="chatwidgetflowfunnel-button <?php echo esc_attr($icon_style); ?>" onclick="toggleChatWidgetFlowfunnelPopup()">
                <i class="fab fa-whatsapp"></i>
            </div>
            <?php if (!empty($inquiry_options)): ?>
                <div class="chatwidgetflowfunnel-popup hidden" id="chatwidgetflowfunnel-popup">
                    <div class="popup-content">
                        <div class="popup-header">
                            <div class="header-icon">
                                <i class="fab fa-whatsapp"></i>
                            </div>
                            <h3>How can we help?</h3>
                        </div>
                        <div class="popup-options">
                            <?php foreach ($inquiry_options as $option): ?>
                                <a href="https://wa.me/<?php echo esc_attr($full_number); ?>?text=<?php echo urlencode($option); ?>"
                                    target="_blank"
                                    class="chat-option"
                                    onclick="trackChatWidgetFlowfunnelClick('<?php echo esc_js($option); ?>')">
                                    <span class="option-text"><?php echo esc_html($option); ?></span>
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            <?php endforeach; ?>
                        </div>
                        <div class="chatwidgetflowfunnel-brand-name">
                            <a href="https://flowfunnel.io" target="_blank">
                                by flowfunnel.io
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
<?php
    }
}
add_action('wp_footer', 'chatwidgetflowfunnel_button');

// Add settings link to plugins page
function chatwidgetflowfunnel_add_settings_link($links)
{
    $settings_link = '<a href="' . admin_url('options-general.php?page=chat-widget-flowfunnel-settings') . '">' . __('Settings', 'chat-widget-flowfunnel') . '</a>';
    array_unshift($links, $settings_link);
    return $links;
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'chatwidgetflowfunnel_add_settings_link');
