/**
 * AI Consultant GROK Chat Frontend
 * Version: 2.5.0
 * Author: Ruslan Bilohash
 */

(function () {
    'use strict';

    if (typeof aiConsultantGrok === 'undefined' || !aiConsultantGrok.ajax_url) {
        console.error('AI Consultant GROK: settings not loaded.');
        return;
    }

    const s = aiConsultantGrok;

    const API_URL = s.ajax_url;
    const NONCE = s.nonce || '';
    const SESSION_KEY = 'ai_consultant_grok_session';

    let session = localStorage.getItem(SESSION_KEY);
    if (!session) {
        session = 's_' + Date.now() + '_' + Math.random().toString(36).substring(2, 16);
        localStorage.setItem(SESSION_KEY, session);
    }

    // Floating button
    const openBtn = document.createElement('button');
    openBtn.id = 'ai-consultant-grok-open-btn';
    openBtn.className = 'ai-consultant-grok-widget';
    openBtn.style.cssText = `
        position: fixed;
        bottom: 28px;
        ${s.position === 'left' ? 'left: 28px;' : 'right: 28px;'}
        width: 78px;
        height: 78px;
        background: ${s.widget_color || '#00f5ff'};
        opacity: ${s.widget_opacity || 1};
        color: #000;
        border: none;
        border-radius: 50%;
        font-size: 42px;
        cursor: pointer;
        box-shadow: 0 20px 50px rgba(0,0,0,0.4);
        z-index: 99998;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    `;
    openBtn.innerHTML = s.bot_icon || '🧹';
    document.body.appendChild(openBtn);

    // Chat window
    const chatContainer = document.createElement('div');
    chatContainer.id = 'ai-consultant-grok-chat';
    chatContainer.style.cssText = `
        position: fixed;
        bottom: 20px;
        ${s.position === 'left' ? 'left: 20px;' : 'right: 20px;'}
        width: 420px;
        max-width: 94vw;
        height: 680px;
        background: ${s.chat_bg_color || '#0f0f2d'};
        backdrop-filter: blur(24px);
        border-radius: 28px;
        box-shadow: 0 25px 80px -15px rgba(0,245,255,0.5);
        display: none;
        flex-direction: column;
        overflow: hidden;
        z-index: 99999;
        border: 1px solid rgba(0,245,255,0.3);
        font-family: system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
        transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    `;

    chatContainer.innerHTML = `
        <div class="ai-chat-header" style="background:${s.header_gradient || 'linear-gradient(135deg,#00f5ff 0%,#0099ff 100%)'};color:#000;padding:20px 24px;display:flex;align-items:center;justify-content:space-between;font-weight:800;font-size:20px;border-radius:28px 28px 0 0;">
            <div style="display:flex;align-items:center;gap:14px;">
                <div style="width:52px;height:52px;background:#000;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:32px;box-shadow:0 0 20px rgba(0,245,255,0.6);">
                    ${s.bot_icon || '🧹'}
                </div>
                <div>
                    <div>${s.chat_title || 'AI Consultant GROK'}</div>
                    <div style="font-size:13.8px;opacity:0.9;">${s.chat_subtitle || 'Your Professional AI Assistant'}</div>
                </div>
            </div>
            <button id="ai-close-chat" style="background:none;border:none;color:#000;font-size:34px;cursor:pointer;line-height:1;padding:0 8px;">×</button>
        </div>
        <div id="ai-messages" style="flex:1;padding:24px 22px;overflow-y:auto;background:rgba(10,10,31,0.95);display:flex;flex-direction:column;gap:16px;"></div>
        <div style="padding:18px 22px;border-top:1px solid rgba(0,245,255,0.25);background:rgba(15,15,45,0.98);display:flex;gap:12px;">
            <input id="ai-input" type="text" placeholder="Type your message..." 
                   style="flex:1;padding:16px 24px;background:rgba(255,255,255,0.1);border:1px solid rgba(0,245,255,0.4);border-radius:9999px;outline:none;font-size:16px;color:#fff;">
            <button id="ai-send-btn" style="background:linear-gradient(90deg,${s.primary_color||'#00f5ff'},${s.accent_color||'#0099ff'});color:#000;border:none;border-radius:9999px;width:60px;height:60px;cursor:pointer;font-size:28px;font-weight:bold;">→</button>
        </div>
        <div style="padding:10px 20px;text-align:center;font-size:12.5px;color:rgba(255,255,255,0.45);background:rgba(0,0,0,0.3);border-top:1px solid rgba(0,245,255,0.2);">
            Powered by <a href="https://bilohash.com/ai/wordpress" target="_blank" style="color:#00f5ff;text-decoration:none;">AI Consultant GROK</a>
        </div>
    `;

    document.body.appendChild(chatContainer);

    const messagesDiv = document.getElementById('ai-messages');
    const inputField  = document.getElementById('ai-input');

    function addMessage(text, sender) {
        const div = document.createElement('div');
        div.style.cssText = `
            max-width:86%; 
            padding:14px 20px; 
            border-radius:22px; 
            line-height:1.55; 
            font-size:15.8px;
            word-wrap: break-word;
            animation: messageIn 0.3s ease;
            ${sender === 'client' 
                ? `align-self:flex-end; background:linear-gradient(90deg,${s.primary_color||'#00f5ff'},${s.accent_color||'#0099ff'}); color:#000;` 
                : `align-self:flex-start; background:rgba(255,255,255,0.18); color:#fff;`}
        `;
        div.textContent = text;
        messagesDiv.appendChild(div);
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    }

    function showLoading() {
        const loading = document.createElement('div');
        loading.id = 'ai-loading';
        loading.style.cssText = `
            align-self:flex-start; 
            background:rgba(255,255,255,0.15); 
            padding:12px 18px; 
            border-radius:20px; 
            font-size:14px;
            color:#aaa;
            animation: pulse 1.5s infinite;
        `;
        loading.textContent = 'AI is thinking...';
        messagesDiv.appendChild(loading);
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
        return loading;
    }

    async function sendMessage() {
        const text = inputField.value.trim();
        if (!text) return;

        addMessage(text, 'client');
        inputField.value = '';

        const loadingEl = showLoading();

        try {
            const formData = new FormData();
            formData.append('action', 'ai_consultant_grok_bot');
            formData.append('session', session);
            formData.append('message', text);
            formData.append('nonce', NONCE);

            const response = await fetch(API_URL, {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            loadingEl.remove();

            if (data.success && data.data && data.data.reply) {
                addMessage(data.data.reply, 'bot');
            } else if (data.data && data.data.message) {
                addMessage('Error: ' + data.data.message, 'bot');
            } else {
                addMessage('Failed to get response from the bot.', 'bot');
            }

        } catch (err) {
            if (loadingEl && loadingEl.parentNode) loadingEl.remove();
            addMessage('Sorry, there was a connection issue. Please try again.', 'bot');
            console.error('AI Consultant GROK Error:', err);
        }
    }

    // Auto open
    if (s.auto_open) {
        setTimeout(() => {
            if (chatContainer.style.display !== 'flex') {
                chatContainer.style.display = 'flex';
                openBtn.style.display = 'none';

                if (messagesDiv.children.length === 0) {
                    setTimeout(() => {
                        addMessage(s.welcome_text || 'Hello! How can I help you today?', 'bot');
                    }, 400);
                }
            }
        }, parseInt(s.auto_open_delay) || 4000);
    }

    openBtn.addEventListener('click', () => {
        chatContainer.style.display = 'flex';
        openBtn.style.display = 'none';
    });

    document.getElementById('ai-close-chat').addEventListener('click', () => {
        chatContainer.style.display = 'none';
        openBtn.style.display = 'block';
    });

    document.getElementById('ai-send-btn').addEventListener('click', sendMessage);

    inputField.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            sendMessage();
        }
    });

    // Keyboard accessibility
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && chatContainer.style.display === 'flex') {
            chatContainer.style.display = 'none';
            openBtn.style.display = 'block';
        }
    });

    console.log('✅ AI Consultant GROK v2.5.0 — successfully initialized');

})();