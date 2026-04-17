=== AI Consultant WP ===
Contributors: ruslanbilohash
Tags: chatbot, ai, grok, telegram, consultant
Requires at least: 6.0
Tested up to: 6.5
Requires PHP: 8.0
Stable tag: 2.3.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Розумний AI-чатбот на базі Grok xAI з підтримкою Telegram. Повністю налаштовується з адмін-панелі.

== Description ==

AI Consultant WP — це плагін для WordPress, який додає на ваш сайт розумного AI-чатбота на базі Grok xAI від компанії xAI (Elon Musk). Плагін підтримує відправку повідомлень у Telegram і повністю налаштовується через зручну адмін-панель.

**Основні можливості:**

* Інтеграція з Grok xAI API для генерації відповідей
* Відправка повідомлень клієнтів та відповідей бота у Telegram
* Налаштування дизайну: кольори, іконка, градієнт заголовка, фон чату
* Плаваючий віджет: позиція (ліво/право), колір, прозорість
* Авто-відкриття чату із затримкою
* Власний системний промпт (інструкція для бота)
* Привітальне повідомлення
* Збереження контексту розмови по сесіях

== Installation ==

1. Завантажте архів плагіна та розпакуйте його.
2. Перемістіть папку `ai-consultant-wp` до директорії `/wp-content/plugins/`.
3. Активуйте плагін через меню «Плагіни» в WordPress.
4. Перейдіть у «AI Consultant» в адмін-меню та введіть ваш Grok xAI API ключ.
5. За бажанням — налаштуйте Telegram для отримання повідомлень.

== Frequently Asked Questions ==

= Де отримати Grok xAI API ключ? =
Перейдіть на [console.x.ai](https://console.x.ai), увійдіть через акаунт X (Twitter) та створіть новий API ключ.

= Як отримати Telegram Chat ID? =
Напишіть @userinfobot у Telegram та надішліть /start — він поверне ваш Chat ID.

= Чи зберігаються розмови? =
Так, розмови зберігаються у JSON-файлах у захищеній директорії всередині плагіна. Директорія захищена від прямого HTTP-доступу через `.htaccess`.

== Changelog ==

= 2.3.4 =
* Виправлено помилку в назві JavaScript-об'єкта (aiConsultantWP), через яку чат не завантажувався
* Замінено прямі виклики cURL та file_get_contents на WordPress HTTP API (wp_remote_post)
* Додано валідацію сесійного ID для захисту від path traversal атак
* Додано автоматичний захист директорій conversations та logs через .htaccess
* Виправлено заголовок плагіна (Author URI, Requires at least, Text Domain)
* Вимкнено sslverify => false в перевірці оновлень
* Видалено некоректні виклики header() з config.php

= 2.3.3 =
* Початковий реліз

== Upgrade Notice ==

= 2.3.4 =
Критичне оновлення безпеки. Рекомендується оновити якомога швидше.
