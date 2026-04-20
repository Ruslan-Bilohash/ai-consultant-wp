<?php
/**
 * AI Consultant GROK - Uninstall Script
 * Version: 2.5.0
 *
 * Completely removes all data when the plugin is deleted.
 *
 * @package AI_Consultant_GROK
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Define path safely
if (!defined('AICON_GROK_PATH')) {
    define('AICON_GROK_PATH', dirname(__FILE__) . '/');
}

// 1. Delete main settings
delete_option('ai_consultant_grok_settings');

// 2. Delete transients (if any were used)
delete_transient('aicon_grok_rate_transients');

// 3. Soft cleanup of rate limit transients
$aicon_grok_rate_prefix = 'aicon_grok_rate_';

for ($aicon_grok_i = 0; $aicon_grok_i < 500; $aicon_grok_i++) {
    $aicon_grok_key = $aicon_grok_rate_prefix . $aicon_grok_i;
    delete_transient($aicon_grok_key);
}

// 4. Delete conversations folder and all JSON files
$aicon_grok_conversations_dir = AICON_GROK_PATH . 'conversations';

if (is_dir($aicon_grok_conversations_dir) && WP_Filesystem()) {
    global $wp_filesystem;

    $aicon_grok_conversation_files = glob($aicon_grok_conversations_dir . '/*.json');

    if (!empty($aicon_grok_conversation_files)) {
        foreach ($aicon_grok_conversation_files as $aicon_grok_file) {
            $wp_filesystem->delete($aicon_grok_file);
        }
    }

    $wp_filesystem->rmdir($aicon_grok_conversations_dir, true);
}

// 5. Delete logs folder and all files
$aicon_grok_logs_dir = AICON_GROK_PATH . 'logs';

if (is_dir($aicon_grok_logs_dir) && WP_Filesystem()) {
    global $wp_filesystem;

    $aicon_grok_log_files = glob($aicon_grok_logs_dir . '/*');

    if (!empty($aicon_grok_log_files)) {
        foreach ($aicon_grok_log_files as $aicon_grok_file) {
            $wp_filesystem->delete($aicon_grok_file);
        }
    }

    $wp_filesystem->rmdir($aicon_grok_logs_dir, true);
}