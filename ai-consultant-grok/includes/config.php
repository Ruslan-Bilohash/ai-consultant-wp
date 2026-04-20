<?php
/**
 * AI Consultant GROK — Configuration File
 * Version: 2.5.0
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!defined('AICON_GROK_PATH')) {
    define('AICON_GROK_PATH', plugin_dir_path(dirname(__FILE__)));
}

if (!defined('AICON_GROK_URL')) {
    define('AICON_GROK_URL', plugin_dir_url(dirname(__FILE__)));
}

// Default settings fallback
if (!function_exists('aicon_grok_default_settings')) {
    function aicon_grok_default_settings() {
        return [
            'enable_chat'         => true,
            'xai_api_key'         => '',
            'telegram_token'      => '',
            'telegram_chat_id'    => 0,
            'primary_color'       => '#00f5ff',
            'accent_color'        => '#0099ff',
            'header_gradient'     => 'linear-gradient(135deg, #00f5ff 0%, #0099ff 100%)',
            'chat_bg_color'       => '#0f0f2d',
            'bot_icon'            => '🧹',
            'chat_title'          => 'AI Consultant GROK',
            'chat_subtitle'       => 'Your Professional AI Assistant',
            'position'            => 'right',
            'widget_color'        => '#00f5ff',
            'widget_opacity'      => 1.0,
            'auto_open'           => true,
            'auto_open_delay'     => 4000,
            'welcome_text'        => "Hello! 🧹 I'm an AI Consultant GROK from bilohash.com. How can I help you today?",
            'system_prompt'       => "You are a professional, friendly and expert AI consultant from bilohash.com, powered by Grok from xAI. You specialize in website development and programming. Respond warmly, clearly and to the point. Always strive to help the client as much as possible.",
            'enable_telegram'     => true,
            'rate_limit'          => 30,
        ];
    }
}

// Load settings
$aicon_grok_settings = get_option('ai_consultant_grok_settings', aicon_grok_default_settings());

// Important constants
define('AICON_GROK_XAI_API_KEY', $aicon_grok_settings['xai_api_key'] ?? '');
define('AICON_GROK_TELEGRAM_TOKEN', $aicon_grok_settings['telegram_token'] ?? '');
define('AICON_GROK_TELEGRAM_CHAT_ID', (int) ($aicon_grok_settings['telegram_chat_id'] ?? 0));

define('AICON_GROK_CONVERSATIONS_DIR', AICON_GROK_PATH . 'conversations');
define('AICON_GROK_LOGS_DIR', AICON_GROK_PATH . 'logs');

// Create necessary folders
if (!is_dir(AICON_GROK_CONVERSATIONS_DIR)) {
    wp_mkdir_p(AICON_GROK_CONVERSATIONS_DIR);
}
if (!is_dir(AICON_GROK_LOGS_DIR)) {
    wp_mkdir_p(AICON_GROK_LOGS_DIR);
}