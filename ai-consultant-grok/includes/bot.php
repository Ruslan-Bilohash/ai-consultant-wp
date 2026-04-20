<?php
/**
 * AI Consultant GROK - Bot Handler
 * Version: 2.5.0
 */

if (!defined('ABSPATH')) {
    exit;
}

check_ajax_referer('ai_consultant_grok_action', 'nonce');

$aicon_grok_session = sanitize_text_field(wp_unslash($_POST['session'] ?? ''));
$aicon_grok_message = trim(sanitize_text_field(wp_unslash($_POST['message'] ?? '')));

if (empty($aicon_grok_message)) {
    wp_send_json_error(['message' => 'Empty message']);
    exit;
}

if (empty(AICON_GROK_XAI_API_KEY)) {
    wp_send_json_error(['message' => 'Grok API key is not configured. Please go to plugin settings → Grok API']);
    exit;
}

$aicon_grok_file = AICON_GROK_CONVERSATIONS_DIR . '/' . sanitize_file_name($aicon_grok_session) . '.json';

$aicon_grok_conversation = file_exists($aicon_grok_file)
    ? json_decode(file_get_contents($aicon_grok_file), true) ?: []
    : [];

$aicon_grok_settings = get_option('ai_consultant_grok_settings', aicon_grok_default_settings());

if (empty($aicon_grok_conversation)) {
    $aicon_grok_conversation[] = [
        'role'    => 'system',
        'content' => $aicon_grok_settings['system_prompt'] ?? 'You are a professional and friendly AI consultant.'
    ];
}

$aicon_grok_conversation[] = [
    'role'    => 'user',
    'content' => $aicon_grok_message,
    'sender'  => 'client'
];

// Telegram notification - client message
if (!empty($aicon_grok_settings['enable_telegram']) && AICON_GROK_TELEGRAM_CHAT_ID > 0 && AICON_GROK_TELEGRAM_TOKEN) {
    $aicon_grok_tg_client = "🟢 <b>Client wrote:</b>\n" . esc_html($aicon_grok_message) 
                          . "\n\nSession: <code>" . esc_html($aicon_grok_session) . "</code>";

    wp_remote_get("https://api.telegram.org/bot" . AICON_GROK_TELEGRAM_TOKEN . "/sendMessage?" . http_build_query([
        'chat_id'    => AICON_GROK_TELEGRAM_CHAT_ID,
        'text'       => $aicon_grok_tg_client,
        'parse_mode' => 'HTML'
    ]));
}

// Prepare messages for Grok API
$aicon_grok_messages_for_api = [];
foreach ($aicon_grok_conversation as $m) {
    if (!empty($m['content'])) {
        $aicon_grok_messages_for_api[] = ['role' => $m['role'], 'content' => $m['content']];
    }
}

// Call Grok API
$aicon_grok_response = wp_remote_post('https://api.x.ai/v1/chat/completions', [
    'headers' => [
        'Content-Type'  => 'application/json',
        'Authorization' => 'Bearer ' . trim(AICON_GROK_XAI_API_KEY),
    ],
    'body'    => wp_json_encode([
        'model'       => 'grok-4.20-0309-non-reasoning',
        'messages'    => $aicon_grok_messages_for_api,
        'temperature' => 0.85,
        'max_tokens'  => 800,
    ]),
    'timeout' => 45,
]);

$aicon_grok_reply = 'Unfortunately, there is a problem connecting to the Grok API. Please try again later.';

if (!is_wp_error($aicon_grok_response)) {
    $aicon_grok_http_code = wp_remote_retrieve_response_code($aicon_grok_response);
    $aicon_grok_body      = wp_remote_retrieve_body($aicon_grok_response);

    if ($aicon_grok_http_code === 200) {
        $aicon_grok_json = json_decode($aicon_grok_body, true);
        $aicon_grok_reply = $aicon_grok_json['choices'][0]['message']['content'] ?? 'Sorry, I could not generate a response.';
    } elseif ($aicon_grok_http_code === 401) {
        $aicon_grok_reply = 'Invalid Grok API key.';
    } elseif ($aicon_grok_http_code === 400) {
        $aicon_grok_reply = 'Grok API rejected the request.';
    }
}

// Save bot reply
$aicon_grok_conversation[] = [
    'role'    => 'assistant',
    'content' => $aicon_grok_reply,
    'sender'  => 'bot'
];

// Telegram notification - bot reply
if (!empty($aicon_grok_settings['enable_telegram']) && AICON_GROK_TELEGRAM_CHAT_ID > 0 && AICON_GROK_TELEGRAM_TOKEN) {
    $aicon_grok_tg_bot = "🧠 <b>Grok replied:</b>\n" . esc_html($aicon_grok_reply) 
                       . "\n\nSession: <code>" . esc_html($aicon_grok_session) . "</code>";

    wp_remote_get("https://api.telegram.org/bot" . AICON_GROK_TELEGRAM_TOKEN . "/sendMessage?" . http_build_query([
        'chat_id'    => AICON_GROK_TELEGRAM_CHAT_ID,
        'text'       => $aicon_grok_tg_bot,
        'parse_mode' => 'HTML'
    ]));
}

file_put_contents($aicon_grok_file, json_encode($aicon_grok_conversation, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

wp_send_json_success(['reply' => $aicon_grok_reply]);