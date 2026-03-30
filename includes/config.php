<?php
if (!defined('ABSPATH')) {
    exit;
}

$settings = get_option('ai_consultant_wp_settings', ai_consultant_wp_default_settings());

define('XAI_API_KEY', $settings['xai_api_key'] ?? '');
define('TELEGRAM_TOKEN', $settings['telegram_token'] ?? '');
define('YOUR_TELEGRAM_CHAT_ID', (int)($settings['telegram_chat_id'] ?? 0));
define('GROK_MODEL', 'grok-4.20-0309-non-reasoning');

define('CONVERSATIONS_DIR', AI_CONSULTANT_WP_PATH . 'conversations');
define('LOG_DIR', AI_CONSULTANT_WP_PATH . 'logs');

if (!is_dir(CONVERSATIONS_DIR)) wp_mkdir_p(CONVERSATIONS_DIR);
if (!is_dir(LOG_DIR)) wp_mkdir_p(LOG_DIR);

// Безпека
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
