<?php
/**
 * AI Consultant GROK - Settings Page
 * Version: 2.5.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * AI Consultant GROK Settings Page
 */
function aicon_grok_settings_page() {
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('Insufficient permissions to access.', 'ai-consultant-grok'));
    }

    // Save settings
    if (isset($_POST['ai_consultant_grok_save'])) {
        check_admin_referer('ai_consultant_grok_save_settings');

        $bot_icon = !empty($_POST['bot_icon_custom'])
            ? sanitize_text_field(wp_unslash($_POST['bot_icon_custom']))
            : sanitize_text_field(wp_unslash($_POST['bot_icon'] ?? '🧹'));

        $new_settings = [
            'enable_chat'        => !empty($_POST['enable_chat']),
            'xai_api_key'        => sanitize_text_field(wp_unslash($_POST['xai_api_key'] ?? '')),
            'telegram_token'     => sanitize_text_field(wp_unslash($_POST['telegram_token'] ?? '')),
            'telegram_chat_id'   => (int) sanitize_text_field(wp_unslash($_POST['telegram_chat_id'] ?? 0)),
            'primary_color'      => sanitize_hex_color(wp_unslash($_POST['primary_color'] ?? '#00f5ff')),
            'accent_color'       => sanitize_hex_color(wp_unslash($_POST['accent_color'] ?? '#0099ff')),
            'header_gradient'    => sanitize_text_field(wp_unslash($_POST['header_gradient'] ?? 'linear-gradient(135deg, #00f5ff 0%, #0099ff 100%)')),
            'chat_bg_color'      => sanitize_hex_color(wp_unslash($_POST['chat_bg_color'] ?? '#0f0f2d')),
            'bot_icon'           => $bot_icon,
            'chat_title'         => sanitize_text_field(wp_unslash($_POST['chat_title'] ?? 'AI Consultant GROK')),
            'chat_subtitle'      => sanitize_text_field(wp_unslash($_POST['chat_subtitle'] ?? 'Your Professional AI Assistant')),
            'position'           => in_array($_POST['position'] ?? 'right', ['left', 'right'], true) 
                                    ? sanitize_text_field(wp_unslash($_POST['position'])) 
                                    : 'right',
            'widget_color'       => sanitize_hex_color(wp_unslash($_POST['widget_color'] ?? '#00f5ff')),
            'widget_opacity'     => min(1.0, max(0.1, floatval(sanitize_text_field(wp_unslash($_POST['widget_opacity'] ?? 1.0))))),
            'auto_open'          => !empty($_POST['auto_open']),
            'auto_open_delay'    => absint(sanitize_text_field(wp_unslash($_POST['auto_open_delay'] ?? 4000))),
            'welcome_text'       => wp_kses_post(wp_unslash($_POST['welcome_text'] ?? '')),
            'system_prompt'      => wp_kses_post(wp_unslash($_POST['system_prompt'] ?? '')),
            'enable_telegram'    => !empty($_POST['enable_telegram'])
        ];

        update_option('ai_consultant_grok_settings', $new_settings);

        echo '<div class="notice notice-success is-dismissible">
                <p style="color:#000000 !important; margin:0; font-size:15px;">
                    <strong>✅ Settings saved successfully!</strong>
                </p>
              </div>';
    }

    $settings = get_option('ai_consultant_grok_settings', aicon_grok_default_settings());
    $current_version = AICON_GROK_VERSION;
    ?>

    <div class="wrap ai-consultant-grok-settings">

        <div class="ai-consultant-grok-header">
            <div class="header-left">
                <span class="dashicons dashicons-broom ai-logo-icon"></span>
                <div>
                    <h1>AI Consultant GROK</h1>
                    <p class="version-info">Version <?php echo esc_html($current_version); ?> for WordPress 6.9+</p>
                </div>
            </div>
           
            <div class="header-right">
                <a href="https://github.com/Ruslan-Bilohash/ai-consultant-wp" 
                   target="_blank" 
                   class="header-btn">
                    <span>🐙</span> GitHub
                </a>
                <a href="https://bilohash.com/ai/wordpress" 
                   target="_blank" 
                   class="header-btn">
                    <span>🌐</span> Plugin Website
                </a>
                <a href="https://bilohash.com/donate.php" 
                   target="_blank" 
                   class="pro-btn">
                    <span>❤️</span> Support
                </a>
            </div>
        </div>

        <form method="post">
            <?php wp_nonce_field('ai_consultant_grok_save_settings'); ?>

            <h2 class="nav-tab-wrapper">
                <a href="#" class="nav-tab nav-tab-active" data-tab="general">🛠 General</a>
                <a href="#" class="nav-tab" data-tab="prompt">📝 Bot Instructions</a>
                <a href="#" class="nav-tab" data-tab="design">🎨 Chat Design</a>
                <a href="#" class="nav-tab" data-tab="widget">📱 Floating Widget</a>
                <a href="#" class="nav-tab" data-tab="grok">🧠 Grok API</a>
                <a href="#" class="nav-tab" data-tab="telegram">📲 Telegram</a>
            </h2>

            <!-- Tab: General -->
            <div id="tab-general" class="tab-content">
                <table class="form-table">
                    <tr>
                        <th scope="row">🟢 Enable Chatbot</th>
                        <td><input type="checkbox" name="enable_chat" <?php checked($settings['enable_chat'] ?? true); ?>></td>
                    </tr>
                    <tr>
                        <th scope="row">📌 Chat Title</th>
                        <td><input type="text" name="chat_title" value="<?php echo esc_attr($settings['chat_title'] ?? 'AI Consultant GROK'); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row">📌 Chat Subtitle</th>
                        <td><input type="text" name="chat_subtitle" value="<?php echo esc_attr($settings['chat_subtitle'] ?? 'Your Professional AI Assistant'); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row">🤖 Bot Icon</th>
                        <td>
                            <select name="bot_icon" style="font-size:28px; width:380px; padding:8px;">
                                <!-- AI / Robots -->
                                <option value="🤖" <?php selected($settings['bot_icon'] ?? '🤖', '🤖'); ?>>🤖 Robot (Classic)</option>
                                <option value="🦾" <?php selected($settings['bot_icon'] ?? '🤖', '🦾'); ?>>🦾 Cyber Robot</option>
                                <option value="🧠" <?php selected($settings['bot_icon'] ?? '🤖', '🧠'); ?>>🧠 Artificial Intelligence</option>
                                <option value="💡" <?php selected($settings['bot_icon'] ?? '🤖', '💡'); ?>>💡 Idea / Genius</option>
                                <!-- Modern / Tech -->
                                <option value="🚀" <?php selected($settings['bot_icon'] ?? '🤖', '🚀'); ?>>🚀 Rocket</option>
                                <option value="⚡" <?php selected($settings['bot_icon'] ?? '🤖', '⚡'); ?>>⚡ Lightning</option>
                                <option value="🔥" <?php selected($settings['bot_icon'] ?? '🤖', '🔥'); ?>>🔥 Fire</option>
                                <option value="🌟" <?php selected($settings['bot_icon'] ?? '🤖', '🌟'); ?>>🌟 Star</option>
                                <option value="✨" <?php selected($settings['bot_icon'] ?? '🤖', '✨'); ?>>✨ Sparkles</option>
                                <!-- Business -->
                                <option value="💼" <?php selected($settings['bot_icon'] ?? '🤖', '💼'); ?>>💼 Business Case</option>
                                <option value="📊" <?php selected($settings['bot_icon'] ?? '🤖', '📊'); ?>>📊 Analytics</option>
                                <!-- Cleaning -->
                                <option value="🧹" <?php selected($settings['bot_icon'] ?? '🤖', '🧹'); ?>>🧹 Broom</option>
                                <option value="🧼" <?php selected($settings['bot_icon'] ?? '🤖', '🧼'); ?>>🧼 Soap</option>
                                <option value="🧽" <?php selected($settings['bot_icon'] ?? '🤖', '🧽'); ?>>🧽 Sponge</option>
                                <option value="🏠" <?php selected($settings['bot_icon'] ?? '🤖', '🏠'); ?>>🏠 House</option>
                                <!-- People -->
                                <option value="👨‍💼" <?php selected($settings['bot_icon'] ?? '🤖', '👨‍💼'); ?>>👨‍💼 Consultant</option>
                                <option value="🙋" <?php selected($settings['bot_icon'] ?? '🤖', '🙋'); ?>>🙋 Support</option>
                            </select>
                            <p class="description">Choose a ready icon or enter any other emoji below.</p>
                            <input type="text" name="bot_icon_custom" value="<?php echo esc_attr($settings['bot_icon'] ?? '🤖'); ?>"
                                   placeholder="Enter your emoji (e.g. 🚀 or 🦾)" 
                                   style="width:380px; font-size:32px; margin-top:10px; padding:8px;">
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Tab: Prompt -->
            <div id="tab-prompt" class="tab-content" style="display:none;">
                <table class="form-table">
                    <tr>
                        <th scope="row">👋 Welcome Message</th>
                        <td>
                            <textarea name="welcome_text" rows="6" style="width:100%;"><?php echo esc_textarea($settings['welcome_text'] ?? ''); ?></textarea>
                            <p class="description">The first message the bot will show to the user.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">🧠 System Prompt</th>
                        <td>
                            <textarea name="system_prompt" rows="24" style="width:100%; font-family:monospace; font-size:13.5px; line-height:1.6;"><?php echo esc_textarea($settings['system_prompt'] ?? ''); ?></textarea>
                            <p class="description"><strong>Most important field!</strong> Defines the bot's behavior.</p>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Tab: Design -->
            <div id="tab-design" class="tab-content" style="display:none;">
                <h3>🎨 Chat Design</h3>
                <p style="margin-bottom:25px;color:#555;">Changes are visible immediately in the live preview.</p>

                <table class="form-table aicon-color-table">
                    <tr>
                        <th scope="row">🎨 Primary Color<br><small>(client messages)</small></th>
                        <td>
                            <div class="color-picker-group">
                                <input type="color" name="primary_color" id="primary_color" value="<?php echo esc_attr($settings['primary_color'] ?? '#00f5ff'); ?>">
                                <span id="primary_color_value" class="color-value"><?php echo esc_html($settings['primary_color'] ?? '#00f5ff'); ?></span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">🎨 Accent Color</th>
                        <td>
                            <div class="color-picker-group">
                                <input type="color" name="accent_color" id="accent_color" value="<?php echo esc_attr($settings['accent_color'] ?? '#0099ff'); ?>">
                                <span id="accent_color_value" class="color-value"><?php echo esc_html($settings['accent_color'] ?? '#0099ff'); ?></span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">🌈 Header Gradient</th>
                        <td>
                            <div class="color-picker-group">
                                <input type="color" name="header_gradient" id="header_gradient" value="<?php echo esc_attr($settings['header_gradient'] ?? '#00f5ff'); ?>">
                                <span id="header_gradient_value" class="color-value"><?php echo esc_html($settings['header_gradient'] ?? 'linear-gradient(...)'); ?></span>
                            </div>
                            <p class="description">Main gradient color</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">🖼 Chat Background Color</th>
                        <td>
                            <div class="color-picker-group">
                                <input type="color" name="chat_bg_color" id="chat_bg_color" value="<?php echo esc_attr($settings['chat_bg_color'] ?? '#0f0f2d'); ?>">
                                <span id="chat_bg_color_value" class="color-value"><?php echo esc_html($settings['chat_bg_color'] ?? '#0f0f2d'); ?></span>
                            </div>
                        </td>
                    </tr>
                </table>

                <!-- Live Preview -->
                <div style="margin-top:35px;padding:25px;background:#f8f9fa;border-radius:16px;">
                    <h4 style="margin-bottom:15px;">🔴 Live Preview</h4>
                    <div id="design-preview" style="max-width:420px;margin:0 auto;background:#0a0f24;border-radius:20px;overflow:hidden;box-shadow:0 20px 60px rgba(0,245,255,0.35);">
                        <div id="preview-header" style="background:<?php echo esc_attr($settings['header_gradient'] ?? 'linear-gradient(135deg, #00f5ff 0%, #0099ff 100%)'); ?>;padding:18px 20px;color:#000;font-weight:600;display:flex;align-items:center;gap:14px;">
                            <span style="font-size:32px;"><?php echo esc_html($settings['bot_icon'] ?? '🧹'); ?></span>
                            <div>
                                <div><?php echo esc_html($settings['chat_title'] ?? 'AI Consultant GROK'); ?></div>
                                <div style="font-size:13.5px;opacity:0.95;"><?php echo esc_html($settings['chat_subtitle'] ?? 'Your Professional AI Assistant'); ?></div>
                            </div>
                        </div>
                        <div id="preview-body" style="height:260px;padding:20px;background:<?php echo esc_attr($settings['chat_bg_color'] ?? '#0f0f2d'); ?>;overflow-y:auto;display:flex;flex-direction:column;gap:14px;">
                            <div style="max-width:75%;background:#1e2a4a;padding:14px 18px;border-radius:16px 16px 16px 4px;align-self:flex-start;color:#fff;">
                                Hello! How can I help you with cleaning?
                            </div>
                            <div id="preview-client-msg" style="max-width:75%;background:<?php echo esc_attr($settings['primary_color'] ?? '#00f5ff'); ?>;color:#000;padding:14px 18px;border-radius:16px 16px 4px 16px;align-self:flex-end;">
                                How much does a general apartment cleaning cost?
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab: Widget -->
            <div id="tab-widget" class="tab-content" style="display:none;">
                <table class="form-table">
                    <tr>
                        <th scope="row">📍 Floating Button Position</th>
                        <td>
                            <select name="position">
                                <option value="right" <?php selected($settings['position'] ?? 'right', 'right'); ?>>Right</option>
                                <option value="left" <?php selected($settings['position'] ?? 'right', 'left'); ?>>Left</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">⏱ Auto-open Chat</th>
                        <td><input type="checkbox" name="auto_open" <?php checked($settings['auto_open'] ?? true); ?>></td>
                    </tr>
                    <tr>
                        <th scope="row">⏱ Auto-open Delay (ms)</th>
                        <td>
                            <input type="number" name="auto_open_delay" value="<?php echo esc_attr($settings['auto_open_delay'] ?? 4000); ?>" step="500">
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Tab: Grok API -->
            <div id="tab-grok" class="tab-content" style="display:none;">
                <h3>🧠 Grok xAI API</h3>
                <p style="margin-bottom:20px;">The chatbot requires a Grok xAI API key to function.</p>

                <table class="form-table">
                    <tr>
                        <th scope="row">🔑 Grok xAI API Key</th>
                        <td>
                            <input type="text" name="xai_api_key" id="xai_api_key" 
                                   value="<?php echo esc_attr($settings['xai_api_key'] ?? ''); ?>" 
                                   class="regular-text" style="width:100%; font-family:monospace;">
                            <p class="description">
                                <strong>How to get the key:</strong><br>
                                1. Go to <a href="https://console.x.ai/" target="_blank">https://console.x.ai/</a><br>
                                2. Log in with your X (Twitter) account<br>
                                3. Click "Create new API key"<br>
                                4. Copy the key and paste it here
                            </p>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Tab: Telegram -->
            <div id="tab-telegram" class="tab-content" style="display:none;">
                <h3>📲 Telegram Notifications</h3>
                <p style="margin-bottom:20px;">The plugin can send all customer messages to you in Telegram.</p>

                <table class="form-table">
                    <tr>
                        <th scope="row">🤖 Telegram Bot Token</th>
                        <td>
                            <input type="text" name="telegram_token" id="telegram_token" 
                                   value="<?php echo esc_attr($settings['telegram_token'] ?? ''); ?>" 
                                   class="regular-text" style="width:100%; font-family:monospace;">
                            <p class="description">
                                <strong>How to create a bot:</strong><br>
                                1. Open Telegram → @BotFather<br>
                                2. Send /newbot<br>
                                3. Copy the token and paste it here
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">👤 Your Telegram Chat ID</th>
                        <td>
                            <input type="text" name="telegram_chat_id" id="telegram_chat_id" 
                                   value="<?php echo esc_attr($settings['telegram_chat_id'] ?? ''); ?>" 
                                   class="regular-text">
                            <p class="description">
                                <strong>How to get your Chat ID:</strong><br>
                                Send any message to the bot, then open:<br>
                                <a href="https://api.telegram.org/bot[TOKEN]/getUpdates" target="_blank">https://api.telegram.org/bot[TOKEN]/getUpdates</a>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">📨 Enable Notifications</th>
                        <td>
                            <label>
                                <input type="checkbox" name="enable_telegram" <?php checked($settings['enable_telegram'] ?? true); ?>>
                                Send all customer messages to me in Telegram
                            </label>
                        </td>
                    </tr>
                </table>
            </div>

            <p class="submit">
                <input type="submit" name="ai_consultant_grok_save" class="button button-primary button-large save-btn" value="💾 Save All Settings">
            </p>
        </form>
    </div>

    <?php
    wp_register_style('aicon-grok-admin', AICON_GROK_URL . 'assets/admin.css', [], AICON_GROK_VERSION);
    wp_enqueue_style('aicon-grok-admin');
    ?>

    <script>
    // Tab switching
    document.querySelectorAll('.nav-tab').forEach(function(tab) {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelectorAll('.nav-tab').forEach(t => t.classList.remove('nav-tab-active'));
            this.classList.add('nav-tab-active');
            document.querySelectorAll('.tab-content').forEach(c => c.style.display = 'none');
            document.getElementById('tab-' + this.dataset.tab).style.display = 'block';
        });
    });

    // Live preview
    document.addEventListener('DOMContentLoaded', function() {
        const primaryInput = document.getElementById('primary_color');
        const headerInput  = document.getElementById('header_gradient');
        const bgInput      = document.getElementById('chat_bg_color');

        const previewClient = document.getElementById('preview-client-msg');
        const previewHeader = document.getElementById('preview-header');
        const previewBody   = document.getElementById('preview-body');

        const primaryValue = document.getElementById('primary_color_value');
        const headerValue  = document.getElementById('header_gradient_value');
        const bgValue      = document.getElementById('chat_bg_color_value');

        function updatePreview() {
            if (previewClient) previewClient.style.background = primaryInput.value;
            if (previewHeader) previewHeader.style.background = headerInput.value;
            if (previewBody)   previewBody.style.background   = bgInput.value;

            if (primaryValue) primaryValue.textContent = primaryInput.value;
            if (headerValue)  headerValue.textContent  = headerInput.value;
            if (bgValue)      bgValue.textContent      = bgInput.value;
        }

        if (primaryInput) primaryInput.addEventListener('input', updatePreview);
        if (headerInput)  headerInput.addEventListener('input', updatePreview);
        if (bgInput)      bgInput.addEventListener('input', updatePreview);

        updatePreview();
    });
    </script>
    <?php
}