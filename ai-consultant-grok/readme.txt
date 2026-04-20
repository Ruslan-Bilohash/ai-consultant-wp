=== AI Consultant GROK ===
Contributors: rbilohash
Donate link: https://bilohash.com/donate.php
Tags: ai, chatbot, grok, xai, telegram
Requires at least: 6.4
Tested up to: 6.9
Stable tag: 2.5.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

AI Consultant GROK is a modern, beautiful and intelligent chatbot for WordPress powered by Grok from xAI with real-time Telegram notifications.

== Description ==

**AI Consultant GROK** is a powerful and fully customizable AI chatbot for WordPress built with Grok (xAI).

It allows you to:
- Use Grok xAI as your smart assistant
- Receive all customer messages instantly in Telegram (client + bot replies)
- Fully customize chat design (colors, gradients, icon, position, auto-open)
- Automatically save conversation history in JSON files

Perfect for cleaning services, consulting businesses, repair services, real estate, and any company that needs fast and intelligent customer support.

**Key Features:**
- Powered by Grok xAI (latest model)
- Real-time Telegram notifications
- Fully responsive & modern design with live preview
- Conversation history saved automatically
- Easy setup – just add your Grok API key
- Secure nonce protection and input sanitization

== Installation ==

1. Upload the plugin to the `/wp-content/plugins/ai-consultant-grok/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to **AI Consultant GROK → Settings**
4. Enter your Grok xAI API Key (get it from https://console.x.ai/)
5. (Optional) Configure Telegram notifications
6. Save settings and test the floating chat widget on your website

== Frequently Asked Questions ==

= Do I need an API key? =
Yes. The plugin requires a Grok xAI API key to generate responses. Without it, the chatbot will not work.

= How do I get a Grok API key? =
1. Go to https://console.x.ai/
2. Log in with your X (Twitter) account
3. Click "Create new API key"
4. Copy the key and paste it into the plugin settings

= Does it send notifications to Telegram? =
Yes. When enabled, every customer message and bot reply is sent to your Telegram instantly.

= Can I customize the appearance? =
Yes. You can change colors, gradients, floating button position, icon, title, subtitle and more. Changes are visible immediately in the live preview.

== Changelog ==

= 2.5.0 =
* Renamed plugin to "AI Consultant GROK" to comply with WordPress.org requirements
* Updated all prefixes, text domain, class names and file references
* Improved security, code standards and Plugin Check compatibility
* Enhanced admin CSS and frontend JavaScript (better animations, responsiveness, accessibility)
* Updated documentation and translation files
* Ready for WordPress.org repository submission

= 2.4.0 =
* Initial public release (as AI Consultant)

== Upgrade Notice ==

= 2.5.0 =
Please update to version 2.5.0. This version renames the plugin to meet WordPress.org naming policy. All previous settings will be automatically migrated.

== External Services ==

This plugin uses the following external services:

1. **Grok xAI API** – https://api.x.ai/
   - Used to generate intelligent chatbot responses
   - Requires an API key from https://console.x.ai/

2. **Telegram Bot API** – https://api.telegram.org/
   - Used to send real-time notifications to the site administrator