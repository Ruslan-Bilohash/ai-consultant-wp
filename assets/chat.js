// assets/chat.js — сучасний дизайн з повною підтримкою налаштувань віджету (версія 2.3.4)
(function () {
    'use strict';

    if (typeof aiConsultantWP === 'undefined') {
        console.error('AI Consultant: налаштування не завантажено');
        return;
    }

    const API_URL = aiConsultantWP.ajax_url;
    const SESSION_KEY = 'ai_consultant_session';

    let session = localStorage.getItem(SESSION_KEY);
    if (!session) {
        session = 's_' + Date.now() + '_' + Math.random().toString(36).substring(2, 16);
        localStorage.setItem(SESSION_KEY, session);
    }

    // === ПЛАВАЮЧА КНОПКА ВІДЖЕТУ ===
    const openBtn = document.createElement('button');
    openBtn.id = 'ai-consultant-open-btn';
    openBtn.style.cssText = `
        position: fixed;
        bottom: 28px;
        ${aiConsultantWP.position === 'left' ? 'left: 28px; right: auto;' : 'right: 28px; left: auto;'}
        width: 78px;
        height: 78px;
        background: ${aiConsultantWP.widget_color || '#00f5ff'};
        opacity: ${aiConsultantWP.widget_opacity || 1};
        color: #000;
        border: none;
        border-radius: 50%;
        font-size: 42px;
        cursor: pointer;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
        z-index: 99998;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    `;
    openBtn.innerHTML = aiConsultantWP.bot_icon || '🧹';
    document.body.appendChild(openBtn);

    // === Вікно чату ===
    const container = document.createElement('div');
    container.id = 'ai-consultant-chat';
    container.style.cssText = `
        position: fixed;
        bottom: 20px;
        ${aiConsultantWP.position === 'left' ? 'left: 20px; right: auto;' : 'right: 20px;'}
        width: 420px;
        max-width: 94vw;
        height: 680px;
        background: ${aiConsultantWP.chat_bg_color || '#0f0f2d'};
        backdrop-filter: blur(24px);
        border-radius: 28px;
        box-shadow: 0 25px 80px -15px rgba(0,245,255,0.5);
        display: none;
        flex-direction: column;
        overflow: hidden;
        z-index: 99999;
        border: 1px solid rgba(0,245,255,0.3);
        font-family: system-ui, -apple-system, sans-serif;
        transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    `;

    const header = document.createElement('div');
    header.style.cssText = `
        background: ${aiConsultantWP.header_gradient || 'linear-gradient(135deg, #00f5ff 0%, #0099ff 100%)'};
        color: #000;
        padding: 20px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-weight: 800;
        font-size: 20px;
        border-radius: 28px 28px 0 0;
    `;
    header.innerHTML = `
        <div style="display:flex;align-items:center;gap:14px;">
            <div style="width:52px;height:52px;background:#000;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:32px;box-shadow:0 0 20px rgba(0,245,255,0.6);">
                ${aiConsultantWP.bot_icon || '🧹'}
            </div>
            <div>
                <div>${aiConsultantWP.chat_title || 'AI Consultant'}</div>
                <div style="font-size:13.8px;opacity:0.9;">${aiConsultantWP.chat_subtitle || 'Profesionalus valymo konsultantas'}</div>
            </div>
        </div>
        <button id="close-chat" style="background:none;border:none;color:#000;font-size:34px;cursor:pointer;line-height:1;padding:0 8px;">×</button>
    `;

    const messages = document.createElement('div');
    messages.style.cssText = `flex:1; padding:24px 22px; overflow-y:auto; background:rgba(10,10,31,0.95); display:flex; flex-direction:column; gap:16px;`;

    const inputArea = document.createElement('div');
    inputArea.style.cssText = `padding:18px 22px; border-top:1px solid rgba(0,245,255,0.25); background:rgba(15,15,45,0.98); display:flex; gap:12px;`;
    inputArea.innerHTML = `
        <input id="chat-input" type="text" placeholder="Напишіть повідомлення..." 
               style="flex:1; padding:16px 24px; background:rgba(255,255,255,0.1); border:1px solid rgba(0,245,255,0.4); border-radius:9999px; outline:none; font-size:16px; color:#fff;">
        <button id="chat-send" style="background:linear-gradient(90deg,${aiConsultantWP.primary_color || '#00f5ff'},${aiConsultantWP.accent_color || '#0099ff'}); color:#000; border:none; border-radius:9999px; width:60px; height:60px; cursor:pointer;font-size:28px;font-weight:bold;">→</button>
    `;

    container.append(header, messages, inputArea);
    document.body.appendChild(container);

    function addMsg(text, from) {
        const div = document.createElement('div');
        div.style.cssText = `
            max-width:86%; padding:14px 20px; border-radius:22px; line-height:1.55; font-size:15.8px;
            ${from === 'client' ? 
                `align-self:flex-end; background:linear-gradient(90deg,${aiConsultantWP.primary_color || '#00f5ff'},${aiConsultantWP.accent_color || '#0099ff'}); color:#000;` : 
                'align-self:flex-start; background:rgba(255,255,255,0.18); color:#fff;'}
        `;
        div.textContent = text;
        messages.appendChild(div);
        messages.scrollTop = messages.scrollHeight;
    }

    async function sendMessage() {
        const input = document.getElementById('chat-input');
        const text = (input.value || '').trim();
        if (!text) return;

        addMsg(text, 'client');
        input.value = '';

        try {
            const formData = new FormData();
            formData.append('action', 'ai_consultant_bot');
            formData.append('session', session);
            formData.append('message', text);
            formData.append('nonce', aiConsultantWP.nonce || '');

            const r = await fetch(API_URL, { method: 'POST', body: formData });
            const data = await r.json();

            if (data.reply) addMsg(data.reply, 'bot');
            else if (data.error) addMsg('Помилка: ' + data.error, 'bot');
        } catch (e) {
            addMsg('Вибачте, проблема зі зв’язком. bilohash.com/ai', 'bot');
        }
    }

    // Авто-відкриття
    if (aiConsultantWP.auto_open) {
        setTimeout(() => {
            if (container.style.display !== 'flex') {
                container.style.display = 'flex';
                openBtn.style.display = 'none';
                setTimeout(() => {
                    if (messages.children.length === 0) addMsg(aiConsultantWP.welcome_text, 'bot');
                }, 600);
            }
        }, aiConsultantWP.auto_open_delay || 4000);
    }

    // Події
    openBtn.onclick = () => {
        container.style.display = 'flex';
        openBtn.style.display = 'none';
    };

    document.getElementById('close-chat').onclick = () => {
        container.style.display = 'none';
        openBtn.style.display = 'block';
    };

    document.getElementById('chat-send').onclick = sendMessage;
    document.getElementById('chat-input').addEventListener('keypress', e => {
        if (e.key === 'Enter') sendMessage();
    });

})();
