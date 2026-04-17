<?php
/**
 * Uninstall AI Consultant WP
 *
 * Runs when the plugin is deleted from the WordPress admin.
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Видаляємо налаштування з бази даних
delete_option('ai_consultant_wp_settings');

// Видаляємо директорії з розмовами та логами
$plugin_dir = plugin_dir_path(__FILE__);

$dirs_to_remove = [
    $plugin_dir . 'conversations',
    $plugin_dir . 'logs',
];

foreach ($dirs_to_remove as $dir) {
    if (is_dir($dir)) {
        $files = glob($dir . '/*');
        if ($files) {
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file); // phpcs:ignore WordPress.WP.AlternativeFunctions
                }
            }
        }
        rmdir($dir);
    }
}
