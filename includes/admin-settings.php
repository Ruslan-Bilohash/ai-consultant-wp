<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * AI Consultant WP 6.9 — Сторінка налаштувань
 * Версія 2.3.4
 * Розробник: Руслан Билогаш
 */

function ai_consultant_wp_settings_page() {
    if (isset($_POST['ai_consultant_save'])) {
        check_admin_referer('ai_consultant_save_settings');
        
        $bot_icon = !empty($_POST['bot_icon_custom'])
            ? sanitize_text_field($_POST['bot_icon_custom'])
            : sanitize_text_field($_POST['bot_icon'] ?? '🧹');

        $new_settings = [
            'enable_chat'       => !empty($_POST['enable_chat']),
            'xai_api_key'       => sanitize_text_field($_POST['xai_api_key'] ?? ''),
            'telegram_token'    => sanitize_text_field($_POST['telegram_token'] ?? ''),
            'telegram_chat_id'  => (int)($_POST['telegram_chat_id'] ?? 0),
            'primary_color'     => sanitize_hex_color($_POST['primary_color'] ?? '#00f5ff'),
            'accent_color'      => sanitize_hex_color($_POST['accent_color'] ?? '#00d4ff'),
            'header_gradient'   => sanitize_text_field($_POST['header_gradient'] ?? 'linear-gradient(135deg, #00f5ff 0%, #00d4ff 100%)'),
            'chat_bg_color'     => sanitize_hex_color($_POST['chat_bg_color'] ?? '#0f0f2d'),
            'bot_icon'          => $bot_icon,
            'chat_title'        => sanitize_text_field($_POST['chat_title'] ?? 'AI Consultant'),
            'chat_subtitle'     => sanitize_text_field($_POST['chat_subtitle'] ?? 'Profesionalus valymo konsultantas'),
            'position'          => sanitize_text_field($_POST['position'] ?? 'right'),
            'widget_color'      => sanitize_hex_color($_POST['widget_color'] ?? '#00f5ff'),
            'widget_opacity'    => floatval($_POST['widget_opacity'] ?? 1.0),
            'auto_open'         => !empty($_POST['auto_open']),
            'auto_open_delay'   => (int)($_POST['auto_open_delay'] ?? 4000),
            'welcome_text'      => wp_kses_post($_POST['welcome_text'] ?? ''),
            'system_prompt'     => wp_kses_post($_POST['system_prompt'] ?? ''),
            'enable_telegram'   => !empty($_POST['enable_telegram'])
        ];

        update_option('ai_consultant_wp_settings', $new_settings);
        echo '<div class="notice notice-success is-dismissible"><p><strong>✅ Налаштування успішно збережено!</strong></p></div>';
    }

    $settings = get_option('ai_consultant_wp_settings', ai_consultant_wp_default_settings());
    $current_version = '2.3.4';

    ?>
    <div class="wrap ai-consultant-settings">

        <!-- ШАПКА З ІКОНКОЮ -->
        <div class="ai-consultant-header">
            <div class="header-left">
                <span class="dashicons dashicons-broom ai-logo-icon"></span>
                <div>
                    <h1>AI Consultant WP</h1>
                    <p class="version-info">Версія <?php echo esc_html($current_version); ?> • Розробник: Руслан Билогаш</p> - Перевірено на WordPress 6.9
                </div>
            </div>
            
            <div class="header-right">
				
          <!-- Жовта кнопка "Запропонувати покращення" -->
<a href="https://github.com/ruslan-bilohash/ai-consultant-wp/issues" 
   target="_blank" 
   class="header-btn yellow-btn">
    <span class="dashicons dashicons-lightbulb"></span>
    Запропонувати покращення
</a>
				<a href="https://github.com/ruslan-bilohash" target="_blank" class="header-btn">
    <span style="font-size:19px; line-height:1;">🐙</span> GitHub
</a>
                <a href="https://bilohash.com/ai" target="_blank" class="header-btn">
                    <span class="dashicons dashicons-admin-plugins"></span> Сайт плагіну
                </a>
                <a href="https://bilohash.com/donate.php" target="_blank" class="header-btn donate-btn">
                    <span class="dashicons dashicons-heart"></span> Підтримати
                </a>
			<a href="https://bilohash.com/donate.php" 
   target="_blank" 
   class="pro-btn">
    <span class="dashicons dashicons-cart"></span>
    PRO
</a>
            </div>
        </div>

        <form method="post">
            <?php wp_nonce_field('ai_consultant_save_settings'); ?>

            <h2 class="nav-tab-wrapper">
                <a href="#" class="nav-tab nav-tab-active" data-tab="general">🛠 Загальні</a>
                <a href="#" class="nav-tab" data-tab="prompt">📝 Інструкція для бота</a>
                <a href="#" class="nav-tab" data-tab="design">🎨 Дизайн чату</a>
                <a href="#" class="nav-tab" data-tab="widget">📱 Плаваючий віджет</a>
                <a href="#" class="nav-tab" data-tab="grok">🧠 Grok API</a>
                <a href="#" class="nav-tab" data-tab="telegram">📲 Telegram</a>
                <a href="#" class="nav-tab" data-tab="updates">🔄 Оновлення</a>
            </h2>

            <!-- Загальні -->
            <div id="tab-general" class="tab-content">
                <table class="form-table">
                    <tr>
                        <th scope="row">🟢 Увімкнути чатбот</th>
                        <td>
                            <input type="checkbox" name="enable_chat" <?php checked($settings['enable_chat']); ?>>
                            <p class="description">Головний перемикач. Якщо вимкнути — чат повністю зникне зі сайту.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">📌 Заголовок чату</th>
                        <td>
                            <input type="text" name="chat_title" value="<?php echo esc_attr($settings['chat_title']); ?>" class="regular-text">
                            <p class="description">Текст, який відображається у верхній частині вікна чату.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">📌 Підзаголовок чату</th>
                        <td>
                            <input type="text" name="chat_subtitle" value="<?php echo esc_attr($settings['chat_subtitle']); ?>" class="regular-text">
                            <p class="description">Короткий опис під заголовком (наприклад: "Profesionalus valymo konsultantas" або "Встановлення AI-віджетів").</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">🧹 Іконка бота</th>
                        <td>
                            <select name="bot_icon" style="font-size:26px;width:340px;">
                                <option value="🧹" <?php selected($settings['bot_icon'], '🧹'); ?>>🧹 Мітла</option>
                                <option value="🧼" <?php selected($settings['bot_icon'], '🧼'); ?>>🧼 Мило</option>
                                <option value="🧽" <?php selected($settings['bot_icon'], '🧽'); ?>>🧽 Губка</option>
                                <option value="✨" <?php selected($settings['bot_icon'], '✨'); ?>>✨ Блиск</option>
                                <option value="🏠" <?php selected($settings['bot_icon'], '🏠'); ?>>🏠 Будинок</option>
                                <option value="🌿" <?php selected($settings['bot_icon'], '🌿'); ?>>🌿 Еко</option>
                                <option value="⚡" <?php selected($settings['bot_icon'], '⚡'); ?>>⚡ Швидкість</option>
                            </select>
                            <p class="description">Оберіть іконку зі списку або вставте будь-який свій емодзі/текст у поле нижче.</p>
                            <input type="text" name="bot_icon_custom" value="<?php echo esc_attr($settings['bot_icon']); ?>"
                                   placeholder="Вставте свій емодзі або текст" style="width:340px; font-size:28px; margin-top:8px;">
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Інструкція для бота -->
            <div id="tab-prompt" class="tab-content" style="display:none;">
                <table class="form-table">
                    <tr>
                        <th scope="row">👋 Привітальне повідомлення</th>
                        <td>
                            <textarea name="welcome_text" rows="6" style="width:100%;"><?php echo esc_textarea($settings['welcome_text']); ?></textarea>
                            <p class="description">Перше повідомлення, яке бот автоматично покаже клієнту при відкритті чату. Зробіть його привітним і корисним.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">🧠 Системний промпт</th>
                        <td>
                            <textarea name="system_prompt" rows="24" style="width:100%; font-family:monospace; font-size:13.5px; line-height:1.6;"><?php echo esc_textarea($settings['system_prompt']); ?></textarea>
                            <p class="description">
                                <strong>Найважливіше поле у всьому плагіні!</strong><br>
                                Тут ви повністю описуєте, хто такий бот, які послуги він пропонує, ціни, стиль спілкування, як збирати контакти клієнта тощо.<br>
                                Чим детальніше написаний промпт — тим розумніше і професійніше працює бот.
                            </p>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Дизайн чату -->
            <div id="tab-design" class="tab-content" style="display:none;">
                <table class="form-table">
                    <tr>
                        <th scope="row">🎨 Основний колір</th>
                        <td>
                            <input type="color" name="primary_color" value="<?php echo esc_attr($settings['primary_color']); ?>" style="width:80px;height:45px;">
                            <p class="description">Основний колір повідомлень клієнта та кнопки відправки.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">🎨 Акцентний колір</th>
                        <td>
                            <input type="color" name="accent_color" value="<?php echo esc_attr($settings['accent_color']); ?>" style="width:80px;height:45px;">
                            <p class="description">Другий колір у градієнтах і акцентах дизайну.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">🌈 Градієнт заголовка</th>
                        <td>
                            <input type="color" name="header_gradient" value="<?php echo esc_attr($settings['header_gradient']); ?>" style="width:80px;height:45px;">
                            <p class="description">Колір градієнта верхньої панелі вікна чату.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">🖼 Колір фону чату</th>
                        <td>
                            <input type="color" name="chat_bg_color" value="<?php echo esc_attr($settings['chat_bg_color']); ?>" style="width:80px;height:45px;">
                            <p class="description">Основний колір фону самого вікна чату.</p>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Плаваючий віджет -->
            <div id="tab-widget" class="tab-content" style="display:none;">
                <table class="form-table">
                    <tr>
                        <th scope="row">📍 Позиція плаваючої кнопки</th>
                        <td>
                            <select name="position">
                                <option value="right" <?php selected($settings['position'], 'right'); ?>>Праворуч (рекомендовано)</option>
                                <option value="left" <?php selected($settings['position'], 'left'); ?>>Ліворуч</option>
                            </select>
                            <p class="description">Де буде розташована кругла кнопка, яка відкриває чат на сайті.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">⏱ Авто-відкриття чату</th>
                        <td><input type="checkbox" name="auto_open" <?php checked($settings['auto_open']); ?>></td>
                    </tr>
                    <tr>
                        <th scope="row">⏱ Затримка авто-відкриття (в мілісекундах)</th>
                        <td>
                            <input type="number" name="auto_open_delay" value="<?php echo esc_attr($settings['auto_open_delay']); ?>" step="500">
                            <p class="description">Через скільки секунд чат відкриється автоматично після завантаження сторінки (рекомендовано 3000–6000).</p>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Grok API -->
            <div id="tab-grok" class="tab-content" style="display:none;">
                <table class="form-table">
                    <tr>
                        <th scope="row">🧠 Grok xAI API Key</th>
                        <td>
                            <input type="text" name="xai_api_key" value="<?php echo esc_attr($settings['xai_api_key']); ?>" class="regular-text" style="width:100%;">
                            <p class="description">
                                Ключ доступу до штучного інтелекту Grok. Без нього бот не зможе генерувати відповіді.<br><br>
                                <strong>Як отримати ключ:</strong><br>
                                1. Перейдіть на <a href="https://console.x.ai" target="_blank">console.x.ai</a><br>
                                2. Увійдіть через акаунт X (Twitter)<br>
                                3. Натисніть "Create new API key"<br>
                                4. Скопіюйте ключ і вставте сюди.
                            </p>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Telegram -->
            <div id="tab-telegram" class="tab-content" style="display:none;">
                <table class="form-table">
                    <tr>
                        <th scope="row">📱 Telegram Bot Token</th>
                        <td>
                            <input type="text" name="telegram_token" value="<?php echo esc_attr($settings['telegram_token']); ?>" class="regular-text" style="width:100%;">
                            <p class="description">
                                Токен вашого Telegram-бота. Необхідний для отримання повідомлень від клієнтів.<br><br>
                                <strong>Як отримати:</strong><br>
                                1. Відкрийте Telegram і знайдіть @BotFather<br>
                                2. Надішліть команду /newbot<br>
                                3. Придумайте назву та username бота<br>
                                4. Скопіюйте отриманий токен і вставте сюди.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">👤 Ваш Telegram Chat ID</th>
                        <td>
                            <input type="number" name="telegram_chat_id" value="<?php echo esc_attr($settings['telegram_chat_id']); ?>">
                            <p class="description">
                                Ваш особистий Chat ID у Telegram. Сюди будуть приходити всі повідомлення від клієнтів і відповіді бота.<br><br>
                                <strong>Як отримати:</strong><br>
                                1. Напишіть @userinfobot у Telegram<br>
                                2. Надішліть /start<br>
                                3. Скопіюйте ваше ID і вставте сюди.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">📨 Увімкнути сповіщення в Telegram</th>
                        <td>
                            <input type="checkbox" name="enable_telegram" <?php checked($settings['enable_telegram']); ?>>
                            <p class="description">Якщо увімкнено — ви будете отримувати в Telegram всі повідомлення від клієнтів та відповіді бота.</p>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- ВКЛАДКА ОНОВЛЕННЯ (оновлена) -->
<div id="tab-updates" class="tab-content" style="display:none;">
    <div class="update-box">
        <h3>🔄 Оновлення плагіну</h3>
        <p><strong>Поточна версія:</strong> <span class="current-ver">v<?php echo esc_html($current_version); ?></span></p>

        <button type="button" id="check-update-btn" class="button button-secondary" style="margin:20px 0 15px 0;">
            🔄 Перевірити оновлення
        </button>

        <div id="update-result" style="min-height:130px;"></div>

        <button type="button" id="download-update-btn" class="button button-primary button-large" style="display:none;">
            ⬇️ Завантажити оновлення
        </button>

        <!-- Нова кнопка "Запропонувати покращення" -->
        <div style="margin-top: 25px; padding-top: 20px; border-top: 1px solid #00f5ff33;">
            <a href="https://github.com/ruslan-bilohash/ai-consultant-wp/issues" 
               target="_blank" 
               class="button button-primary suggest-btn" 
               style="background: linear-gradient(135deg, #ffeb3b, #ffc107); color: #000; font-weight: 600; padding: 10px 20px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                <span class="dashicons dashicons-lightbulb" style="font-size: 18px;"></span>
                Запропонувати покращення
            </a>
        </div>
    </div>
</div>

            <p class="submit">
    <input type="submit" 
           name="ai_consultant_save" 
           class="button button-primary button-large save-btn" 
           value="💾 Зберегти всі налаштування">
</p>
        </form>
    </div>

    <!-- Стилі -->
    <style>
        .ai-consultant-header {
            background: linear-gradient(135deg, #0a0a2e 0%, #1f1f52 100%);
            color: #ffffff;
            padding: 32px 30px;
            border-radius: 16px;
            margin-bottom: 35px;
            box-shadow: 0 12px 45px rgba(0, 245, 255, 0.3);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 25px;
        }
        .ai-logo-icon {
            font-size: 78px;
            width: 78px;
            height: 78px;
            color: #ffffff;
            filter: drop-shadow(0 0 15px #00f5ff);
        }
        .header-left h1 {
            margin: 0 0 8px 0;
            font-size: 36px;
            font-weight: 700;
            color: #ffffff;
        }
        .version-info {
            margin: 0;
            font-size: 16.5px;
            color: #ffffff;
            opacity: 0.95;
        }
        .header-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: rgba(255,255,255,0.15);
            color: #ffffff;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 500;
            transition: all 0.3s;
        }
        .header-btn:hover {
            background: #00f5ff;
            color: #0a0a2e;
        }
        .donate-btn:hover {
            background: #ff4da6;
        }
        .nav-tab-wrapper { margin: 25px 0 35px; }
        .tab-content { padding: 25px 0; }
        .form-table th { width: 260px; vertical-align: top; padding-top: 12px; }
        .description { font-size: 13.8px; color: #555; margin-top: 8px; line-height: 1.45; }
        .update-box {
            background: #111133;
            padding: 32px;
            border-radius: 14px;
            border: 1px solid #00f5ff44;
            color: #ffffff;
        }
        .update-box h3, .update-box p, .update-box strong {
            color: #ffffff;
        }

        /* Чорний текст для всіх повідомлень */
        .notice-warning p,
        .notice-info p,
        .notice-success p {
            color: #000000 !important;
            font-weight: 500;
        }
		.yellow-btn {
    background: linear-gradient(135deg, #ffeb3b, #ffc107) !important;
    color: #000000 !important;
    font-weight: 600;
    border: none;
    box-shadow: 0 4px 12px rgba(255, 193, 7, 0.4);
}

.yellow-btn:hover {
    background: linear-gradient(135deg, #ffe000, #ffb300) !important;
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(255, 193, 7, 0.6);
}
		/* Професійна червона кнопка PRO */
.pro-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: linear-gradient(135deg, #ff1a4d, #e60033);
    color: #ffffff;
    padding: 12px 28px;
    border-radius: 50px;
    font-weight: 700;
    font-size: 16px;
    text-decoration: none;
    transition: all 0.4s ease;
    box-shadow: 0 6px 20px rgba(255, 26, 77, 0.45);
    letter-spacing: 0.5px;
    text-transform: uppercase;
    border: 2px solid rgba(255, 255, 255, 0.2);
}

.pro-btn:hover {
    background: linear-gradient(135deg, #e60033, #c40029);
    transform: translateY(-4px) scale(1.08);
    box-shadow: 0 12px 30px rgba(255, 26, 77, 0.6);
    color: #fff;
}

.pro-btn .dashicons {
    font-size: 20px;
    line-height: 1;
}
/* Професійна червона кнопка PRO */
.pro-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: linear-gradient(135deg, #ff1a4d, #e60033);
    color: #ffffff;
    padding: 12px 28px;
    border-radius: 50px;
    font-weight: 700;
    font-size: 16px;
    text-decoration: none;
    transition: all 0.4s ease;
    box-shadow: 0 6px 20px rgba(255, 26, 77, 0.45);
    letter-spacing: 0.5px;
    text-transform: uppercase;
    border: 2px solid rgba(255, 255, 255, 0.2);
}

.pro-btn:hover {
    background: linear-gradient(135deg, #e60033, #c40029);
    transform: translateY(-4px) scale(1.08);
    box-shadow: 0 12px 30px rgba(255, 26, 77, 0.6);
    color: #fff;
}

.pro-btn .dashicons {
    font-size: 20px;
    line-height: 1;
}
		/* Гарна зелена кнопка "Зберегти всі налаштування" */
.save-btn {
    background: linear-gradient(135deg, #00cc66, #00aa55) !important;
    color: #ffffff !important;
    border: none !important;
    padding: 16px 32px !important;
    font-size: 17px !important;
    font-weight: 700 !important;
    border-radius: 50px !important;
    box-shadow: 0 8px 25px rgba(0, 204, 102, 0.4) !important;
    transition: all 0.4s ease !important;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    cursor: pointer;
}

.save-btn:hover {
    background: linear-gradient(135deg, #00aa55, #008844) !important;
    transform: translateY(-4px) scale(1.05) !important;
    box-shadow: 0 12px 35px rgba(0, 204, 102, 0.55) !important;
    color: #ffffff !important;
}

.save-btn:active {
    transform: scale(0.98) !important;
}

/* Якщо потрібно зробити кнопку ще ширшою */
.submit {
    margin-top: 40px;
}
</style>

    <script>
    // Перемикання табів
    document.querySelectorAll('.nav-tab').forEach(function(tab) {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelectorAll('.nav-tab').forEach(t => t.classList.remove('nav-tab-active'));
            this.classList.add('nav-tab-active');
            document.querySelectorAll('.tab-content').forEach(c => c.style.display = 'none');
            document.getElementById('tab-' + this.dataset.tab).style.display = 'block';
        });
    });

    // Перевірка оновлення
    document.getElementById('check-update-btn').addEventListener('click', function() {
        const btn = this;
        const resultDiv = document.getElementById('update-result');

        btn.disabled = true;
        btn.textContent = '🔄 Перевірка...';

        jQuery.post(ajaxurl, {
            action: 'ai_consultant_check_version',
            nonce: '<?php echo wp_create_nonce('ai_check_version'); ?>'
        }, function(response) {
            if (response.success) {
                const latest = response.data.version.trim();
                const current = '<?php echo $current_version; ?>';

                if (latest > current) {
                    resultDiv.innerHTML = `<div class="notice notice-warning inline"><p>🎉 Доступна нова версія v${latest}!<br>Будь ласка, оновіться до останньої версії.</p></div>`;
                    document.getElementById('download-update-btn').style.display = 'inline-block';
                } else {
                    resultDiv.innerHTML = `<div class="notice notice-success inline"><p>👏 Ви молодець!<br>У вас встановлена остання версія!<br>Так тримати!</p></div>`;
                    document.getElementById('download-update-btn').style.display = 'none';
                }
            } else {
                resultDiv.innerHTML = `<div class="notice notice-error inline"><p>❌ ${response.data.message || 'Не вдалося перевірити оновлення.'}</p></div>`;
            }
        }).fail(function() {
            resultDiv.innerHTML = `<div class="notice notice-error inline"><p>❌ Не вдалося перевірити оновлення.<br>Перевірте підключення або спробуйте пізніше.</p></div>`;
        }).always(function() {
            btn.disabled = false;
            btn.textContent = '🔄 Перевірити оновлення';
        });
    });

    // Заглушка для завантаження
    document.getElementById('download-update-btn').addEventListener('click', function() {
        const msg = document.getElementById('update-result');
        msg.innerHTML += '<div class="notice notice-info"><p>🔧 Автоматичне завантаження в розробці. Оновлення можна завантажити вручну з bilohash.com/ai</p></div>';
    });
    </script>
    <?php
}

// AJAX обробник
add_action('wp_ajax_ai_consultant_check_version', 'ai_consultant_check_version_ajax');

function ai_consultant_check_version_ajax() {
    check_ajax_referer('ai_check_version', 'nonce');

    $response = wp_remote_get('https://bilohash.com/ai/versione.php', [
        'timeout'   => 15,
        'sslverify' => false
    ]);

    if (is_wp_error($response)) {
        wp_send_json_error(['message' => 'Не вдалося підключитися до сервера оновлень.']);
    }

    $latest_version = trim(wp_remote_retrieve_body($response));

    if (empty($latest_version)) {
        wp_send_json_error(['message' => 'Сервер повернув порожню відповідь.']);
    }

    wp_send_json_success(['version' => $latest_version]);
}
?>
