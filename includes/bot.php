<?php
if (!defined('ABSPATH')) {
    exit;
}

// === Отримуємо дані ===
$session = sanitize_text_field($_POST['session'] ?? '');
$message = trim($_POST['message'] ?? '');

if (empty($message)) {
    wp_send_json(['error' => 'Порожнє повідомлення']);
    exit;
}

// Перевірка формату сесії для запобігання path traversal атак
if (!$session || !preg_match('/^s_\d+_[a-z0-9]{6,16}$/i', $session)) {
    wp_send_json(['error' => 'Невірний формат сесії']);
    exit;
}

$file = CONVERSATIONS_DIR . '/' . $session . '.json';
$data = file_exists($file) ? json_decode(file_get_contents($file), true) ?: [] : [];

$settings = get_option('ai_consultant_wp_settings', ai_consultant_wp_default_settings());

if (empty($data)) {
    $data[] = ['role' => 'system', 'content' => $settings['system_prompt'] ?? 'Ти корисний помічник.'];
}

$data[] = ['role' => 'user', 'content' => $message, 'sender' => 'client'];

// === Відправка клієнта в Telegram ===
if (!empty($settings['enable_telegram']) && YOUR_TELEGRAM_CHAT_ID > 0 && TELEGRAM_TOKEN) {
    $tg_client = "🟢 <b>Клієнт написав:</b>\n" . htmlspecialchars($message) . "\n\nSession: <code>$session</code>";
    wp_remote_post("https://api.telegram.org/bot" . TELEGRAM_TOKEN . "/sendMessage", [
        'timeout' => 15,
        'body'    => [
            'chat_id'    => YOUR_TELEGRAM_CHAT_ID,
            'text'       => $tg_client,
            'parse_mode' => 'HTML',
        ],
    ]);
}

// === Запит до Grok ===
$messages_for_api = [];
foreach ($data as $m) {
    if (!empty($m['content'])) {
        $messages_for_api[] = ['role' => $m['role'], 'content' => $m['content']];
    }
}

$api_response = wp_remote_post('https://api.x.ai/v1/chat/completions', [
    'timeout' => 60,
    'headers' => [
        'Content-Type'  => 'application/json',
        'Authorization' => 'Bearer ' . trim(XAI_API_KEY),
    ],
    'body' => wp_json_encode([
        'model'       => GROK_MODEL,
        'messages'    => $messages_for_api,
        'temperature' => 0.85,
        'max_tokens'  => 700,
    ]),
]);

$http = is_wp_error($api_response) ? 0 : wp_remote_retrieve_response_code($api_response);
$resp = is_wp_error($api_response) ? '' : wp_remote_retrieve_body($api_response);

if ($http === 200) {
    $json = json_decode($resp, true);
    $reply = $json['choices'][0]['message']['content'] ?? 'Вибачте, технічна затримка.';
} else {
    $reply = 'На жаль, зараз технічна проблема. Спробуйте пізніше або зверніться до нас напряму.';
}

$data[] = ['role' => 'assistant', 'content' => $reply, 'sender' => 'bot'];

// === Відправка відповіді бота в Telegram ===
if (!empty($settings['enable_telegram']) && YOUR_TELEGRAM_CHAT_ID > 0 && TELEGRAM_TOKEN) {
    $tg_bot = "🧠 <b>Grok відповів:</b>\n" . htmlspecialchars($reply) . "\n\nSession: <code>$session</code>";
    wp_remote_post("https://api.telegram.org/bot" . TELEGRAM_TOKEN . "/sendMessage", [
        'timeout' => 15,
        'body'    => [
            'chat_id'    => YOUR_TELEGRAM_CHAT_ID,
            'text'       => $tg_bot,
            'parse_mode' => 'HTML',
        ],
    ]);
}

file_put_contents($file, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

wp_send_json(['reply' => $reply]);
