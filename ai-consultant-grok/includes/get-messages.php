<?php
/**
 * AI Consultant GROK - Get Conversation History
 * Version: 2.5.0
 *
 * Returns JSON with conversation history for a specific session.
 *
 * @package AI_Consultant_GROK
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

header('Content-Type: application/json; charset=utf-8');

// Security check - nonce verification
if (!isset($_GET['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['nonce'] ?? '')), 'ai_consultant_grok_action')) {
    http_response_code(403);
    echo wp_json_encode(['error' => 'Security check failed']);
    exit;
}

$aicon_grok_session = isset($_GET['session']) 
    ? sanitize_text_field(wp_unslash($_GET['session'])) 
    : '';

// Validate session format
if (empty($aicon_grok_session) || !preg_match('/^s_\d+_[a-z0-9]{6,16}$/i', $aicon_grok_session)) {
    http_response_code(400);
    echo wp_json_encode(['error' => 'Invalid session format']);
    exit;
}

$aicon_grok_file = AICON_GROK_CONVERSATIONS_DIR . '/' . sanitize_file_name($aicon_grok_session) . '.json';

if (!file_exists($aicon_grok_file)) {
    echo wp_json_encode([]);
    exit;
}

$aicon_grok_raw_data = json_decode(file_get_contents($aicon_grok_file), true) ?: [];
$aicon_grok_output   = [];

// Fixed: All variables now properly prefixed with aicon_grok_
foreach ($aicon_grok_raw_data as $aicon_grok_row) {
    if (!empty($aicon_grok_row['content']) && in_array($aicon_grok_row['sender'] ?? '', ['client', 'bot'], true)) {
        $aicon_grok_output[] = [
            'sender'  => $aicon_grok_row['sender'],
            'content' => $aicon_grok_row['content']
        ];
    }
}

echo wp_json_encode($aicon_grok_output, JSON_UNESCAPED_UNICODE);
