<?php
/**
 * Plugin Name: AI Consultant WP
 * Plugin URI: https://bilohash.com/ai
 * Author URI: https://bilohash.com/
 * Description: Розумний AI-чатбот з Grok xAI та Telegram. Повністю налаштовується з адмін-панелі: ключі, кольори, іконка, позиція віджету, прозорість, авто-відкриття тощо.
 * Version: 2.3.4
 * Author: Ruslan Bilohash
 * Text Domain: ai-consultant-wp
 * Requires PHP: 8.0
 * Requires at least: 6.0
 */

if (!defined('ABSPATH')) {
    exit;
}

define('AI_CONSULTANT_WP_VERSION', '2.3.4');
define('AI_CONSULTANT_WP_PATH', plugin_dir_path(__FILE__));
define('AI_CONSULTANT_WP_URL', plugin_dir_url(__FILE__));

// Дефолтні налаштування
if (!function_exists('ai_consultant_wp_default_settings')) {
    function ai_consultant_wp_default_settings() {
        return [
            'enable_chat'       => true,
            'xai_api_key'       => '',
            'telegram_token'    => '',
            'telegram_chat_id'  => 0,

            'primary_color'     => '#00f5ff',
            'accent_color'      => '#0099ff',
            'header_gradient'   => 'linear-gradient(135deg, #00f5ff 0%, #0099ff 100%)',
            'chat_bg_color'     => '#0f0f2d',
            'bot_icon'          => '🧹',
            'chat_title'        => 'AI Consultant',
            'chat_subtitle'     => 'Profesionalus valymo konsultantas',

            'position'          => 'right',
            'widget_color'      => '#00f5ff',
            'widget_opacity'    => 1.0,

            'auto_open'         => true,
            'auto_open_delay'   => 4000,
            'welcome_text'      => "Sveiki! 🧹 Aš esu AI konsultantas iš Ukrbud.lt. Ar norėtumėte užsisakyti profesionalų valymą?",
            'system_prompt'     => "Ти — професійний, дружній AI-консультант компанії Ukrbud.lt...",
            'enable_telegram'   => true
        ];
    }
}

// Підключення файлів
require_once AI_CONSULTANT_WP_PATH . 'includes/config.php';
require_once AI_CONSULTANT_WP_PATH . 'includes/admin-settings.php';

// Посилання "Налаштування" біля плагіна
add_filter('plugin_action_links_' . plugin_basename(__FILE__), function($links) {
    $settings_link = '<a href="' . admin_url('admin.php?page=ai-consultant-wp') . '"><strong>Налаштування</strong></a>';
    array_unshift($links, $settings_link);
    return $links;
});

// Реєстрація сторінки в адмінці
add_action('admin_menu', function() {
    add_menu_page(
        'AI Consultant', 
        'AI Consultant', 
        'manage_options', 
        'ai-consultant-wp', 
        'ai_consultant_wp_settings_page', 
        'dashicons-broom', 
        58
    );
});

// Підключення скриптів на фронтенді
add_action('wp_enqueue_scripts', 'ai_consultant_wp_enqueue_scripts');
function ai_consultant_wp_enqueue_scripts() {
    $settings = get_option('ai_consultant_wp_settings', ai_consultant_wp_default_settings());

    if (empty($settings['enable_chat'])) {
        return;
    }

    wp_enqueue_script(
        'ai-consultant-chat', 
        AI_CONSULTANT_WP_URL . 'assets/chat.js', 
        [], 
        AI_CONSULTANT_WP_VERSION,
        true
    );

    wp_localize_script('ai-consultant-chat', 'aiConsultantWP', [
        'ajax_url'         => admin_url('admin-ajax.php'),
        'nonce'            => wp_create_nonce('ai_consultant_action'),
        
        // Дизайн чату
        'primary_color'    => $settings['primary_color'],
        'accent_color'     => $settings['accent_color'],
        'header_gradient'  => $settings['header_gradient'],
        'chat_bg_color'    => $settings['chat_bg_color'],
        'bot_icon'         => $settings['bot_icon'],
        'chat_title'       => $settings['chat_title'],
        'chat_subtitle'    => $settings['chat_subtitle'],
        
        // Плаваючий віджет
        'position'         => $settings['position'],
        'widget_color'     => $settings['widget_color'],
        'widget_opacity'   => $settings['widget_opacity'],
        
        // Поведінка
        'auto_open'        => (bool)$settings['auto_open'],
        'auto_open_delay'  => (int)$settings['auto_open_delay'],
        'welcome_text'     => wp_kses_post($settings['welcome_text'])
    ]);
}

// AJAX обробник
add_action('wp_ajax_ai_consultant_bot', 'ai_consultant_wp_bot_handler');
add_action('wp_ajax_nopriv_ai_consultant_bot', 'ai_consultant_wp_bot_handler');

function ai_consultant_wp_bot_handler() {
    check_ajax_referer('ai_consultant_action', 'nonce');
    require_once AI_CONSULTANT_WP_PATH . 'includes/bot.php';
    exit;
}
