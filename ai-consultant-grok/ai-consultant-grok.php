<?php
/**
 * Plugin Name:       AI Consultant GROK
 * Plugin URI:        https://bilohash.com/ai/wordpress
 * Description:       Modern Grok (xAI) powered chatbot for WordPress with real-time Telegram notifications. Fully customizable design.
 * Version:           2.5.0
 * Author:            Ruslan Bilohash
 * Author URI:        https://bilohash.com
 * Author Email:      email@bilohash.com
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       ai-consultant-grok
 * Domain Path:       /languages
 * Requires at least: 6.4
 * Tested up to:      6.9
 * Requires PHP:      7.4
 */

if (!defined('ABSPATH')) {
    exit;
}

// Plugin constants
define('AICON_GROK_VERSION', '2.5.0');
define('AICON_GROK_PATH', plugin_dir_path(__FILE__));
define('AICON_GROK_URL',  plugin_dir_url(__FILE__));

// Include core files
require_once AICON_GROK_PATH . 'includes/config.php';
require_once AICON_GROK_PATH . 'includes/admin-settings.php';

// Default settings
if (!function_exists('aicon_grok_default_settings')) {
    function aicon_grok_default_settings() {
        return [
            'enable_chat'       => true,
            'xai_api_key'       => '',
            'telegram_token'    => '',
            'telegram_chat_id'  => 0,
            'primary_color'     => '#00f5ff',
            'accent_color'      => '#0099ff',
            'header_gradient'   => '#00a8e0',
            'chat_bg_color'     => '#0f0f2d',
            'bot_icon'          => '🧹',
            'chat_title'        => 'AI Consultant GROK',
            'chat_subtitle'     => 'Your Professional AI Assistant',
            'position'          => 'right',
            'widget_color'      => '#00f5ff',
            'widget_opacity'    => 1.0,
            'auto_open'         => true,
            'auto_open_delay'   => 4000,
            'welcome_text'      => "Hello! 🧹 I'm an AI Consultant GROK from bilohash.com. How can I help you today?",
            'system_prompt'     => "You are a professional, friendly and expert AI consultant from bilohash.com, powered by Grok from xAI. You specialize in website development and programming. Respond in English or Ukrainian warmly, clearly and to the point. Always strive to help the client as much as possible.",
            'enable_telegram'   => true,
            'rate_limit'        => 30,
        ];
    }
}

// Activation hook
function aicon_grok_activate() {
    if (!get_option('ai_consultant_grok_settings')) {
        add_option('ai_consultant_grok_settings', aicon_grok_default_settings());
    }

    $dirs = [AICON_GROK_PATH . 'conversations', AICON_GROK_PATH . 'logs'];
    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            wp_mkdir_p($dir);
        }
    }
}
register_activation_hook(__FILE__, 'aicon_grok_activate');

// Frontend assets
function aicon_grok_enqueue_frontend_assets() {
    $settings = get_option('ai_consultant_grok_settings', aicon_grok_default_settings());

    if (empty($settings['enable_chat'])) {
        return;
    }

    wp_enqueue_script(
        'aicon-grok-chat',
        AICON_GROK_URL . 'assets/chat.js',
        [],
        AICON_GROK_VERSION,
        true
    );

    wp_localize_script('aicon-grok-chat', 'aiConsultantGrok', [
        'ajax_url'        => admin_url('admin-ajax.php'),
        'nonce'           => wp_create_nonce('ai_consultant_grok_action'),
        'primary_color'   => esc_attr($settings['primary_color']),
        'accent_color'    => esc_attr($settings['accent_color']),
        'header_gradient' => esc_attr($settings['header_gradient']),
        'chat_bg_color'   => esc_attr($settings['chat_bg_color']),
        'bot_icon'        => esc_html($settings['bot_icon']),
        'chat_title'      => esc_html($settings['chat_title']),
        'chat_subtitle'   => esc_html($settings['chat_subtitle']),
        'position'        => esc_attr($settings['position']),
        'widget_color'    => esc_attr($settings['widget_color']),
        'widget_opacity'  => (float) $settings['widget_opacity'],
        'auto_open'       => (bool) $settings['auto_open'],
        'auto_open_delay' => (int) ($settings['auto_open_delay'] ?? 4000),
        'welcome_text'    => wp_kses_post($settings['welcome_text']),
        'is_rtl'          => is_rtl(),
    ]);
}
add_action('wp_enqueue_scripts', 'aicon_grok_enqueue_frontend_assets');

// AJAX handlers
function aicon_grok_bot_handler() {
    require_once AICON_GROK_PATH . 'includes/bot.php';
}
add_action('wp_ajax_ai_consultant_grok_bot', 'aicon_grok_bot_handler');
add_action('wp_ajax_nopriv_ai_consultant_grok_bot', 'aicon_grok_bot_handler');

// Admin menu
function aicon_grok_admin_menu() {
    add_menu_page(
        'AI Consultant GROK',
        'AI Consultant GROK',
        'manage_options',
        'ai-consultant-grok',
        'aicon_grok_settings_page',
        'dashicons-bubble',
        58
    );
}
add_action('admin_menu', 'aicon_grok_admin_menu');

// Plugin action links
function aicon_grok_action_links($links) {
    $settings_link = '<a href="' . esc_url(admin_url('admin.php?page=ai-consultant-grok')) . '"><strong>Settings</strong></a>';
    array_unshift($links, $settings_link);
    return $links;
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'aicon_grok_action_links');