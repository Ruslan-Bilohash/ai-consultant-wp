/**
 * CRM AI Consultant Chat Widget
 * Version: 2.6.9 — Футер всередині чату (як ти просив)
 */

(function () {
    'use strict';

    if (!window.crmAI || !window.crmAI.site_id) {
        console.error('CRM AI: crmAI not initialized');
        return;
    }

    const s = window.crmAI;
    const SESSION_KEY = 'crm_ai_session_' + s.site_id;
    let session = localStorage.getItem(SESSION_KEY);

    if (!session) {
        session = 's_' + Date.now() + '_' + Math.random().toString(36).substr(2, 12);
        localStorage.setItem(SESSION_KEY, session);
    }

    console.log('✅ CRM AI Consultant: віджет запущено для', s.site_id);

    // Плаваюча кнопка
    const openBtn = document.createElement('button');
    openBtn.style.cssText = `
        position:fixed; bottom:25px; ${s.position==='left'?'left:25px':'right:25px'};
        width:68px; height:68px; background:${s.widget_color || '#22d3ee'}; color:#fff;
        border:none; border-radius:50%; font-size:34px; cursor:pointer; z-index:99999;
        box-shadow:0 10px 30px rgba(0,0,0,0.4);
    `;
    openBtn.innerHTML = s.bot_icon || '🤖';
    document.body.appendChild(openBtn);

    // Вікно чату
    const chat = document.createElement('div');
    chat.style.cssText = `
        position:fixed; bottom:20px; ${s.position==='left'?'left:20px':'right:20px'};
        width:400px; max-width:92vw; height:620px; max-height:85vh;
        background:${s.chat_bg_color || '#0f172a'}; 
        border-radius:24px; 
        box-shadow:0 20px 60px rgba(0,0,0,0.6);
        display:none; flex-direction:column; z-index:99999; overflow:hidden;
        border:1px solid rgba(148,163,184,0.3);
    `;

    chat.innerHTML = `
        <div style="background:${s.header_bg_color || '#1e2937'}; padding:18px 20px; color:#fff; display:flex; align-items:center; justify-content:space-between;">
            <div style="display:flex; align-items:center; gap:12px;">
                <div style="font-size:32px;">${s.bot_icon || '🤖'}</div>
                <div>
                    <div style="font-weight:700;">${s.chat_title || 'AI Consultant'}</div>
                    <div style="font-size:13px; opacity:0.85;">${s.chat_subtitle || 'Швидка допомога'}</div>
                </div>
            </div>
            <button id="closeBtn" style="background:none; border:none; color:#fff; font-size:28px; cursor:pointer;">×</button>
        </div>
        
        <div id="messages" style="flex:1; padding:20px; overflow-y:auto; background:${s.chat_bg_color || '#0f172a'};"></div>
        
        <!-- Кнопка відправки -->
        <div style="padding:15px 20px; background:rgba(15,23,42,0.98); display:flex; gap:10px;">
            <input id="inputField" type="text" placeholder="Напишіть повідомлення..." 
                   style="flex:1; padding:14px 18px; background:rgba(255,255,255,0.1); border:1px solid rgba(148,163,184,0.4); border-radius:9999px; color:#fff; outline:none;">
            <button id="sendBtn" style="width:56px; height:56px; background:${s.widget_color || '#22d3ee'}; border:none; border-radius:9999px; color:#000; font-size:24px;">→</button>
        </div>

        <!-- Футер всередині чату (як ти просив) -->
        <div style="padding:8px 20px; text-align:center; font-size:11px; color:#64748b; background:rgba(0,0,0,0.4); border-top:1px solid rgba(148,163,184,0.2);">
            Powered by <a href="https://bilohash.com/" target="_blank" style="color:#67e8f9; text-decoration:none;">CRM AI Consultant</a>
        </div>
    `;

    document.body.appendChild(chat);

    const messagesDiv = document.getElementById('messages');
    const inputField = document.getElementById('inputField');

    function addMessage(text, isUser) {
        const div = document.createElement('div');
        div.style.cssText = isUser 
            ? `margin-left:auto; background:${s.user_bubble_color || '#22d3ee'}; color:#000; padding:12px 18px; border-radius:18px 18px 4px 18px; max-width:80%; margin-bottom:10px;`
            : `margin-right:auto; background:${s.bot_bubble_color || '#334155'}; color:#fff; padding:12px 18px; border-radius:18px 18px 18px 4px; max-width:80%; margin-bottom:10px;`;
        div.textContent = text;
        messagesDiv.appendChild(div);
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    }

    async function sendMessage() {
        const text = inputField.value.trim();
        if (!text) return;

        addMessage(text, true);
        inputField.value = '';

        const fd = new FormData();
        fd.append('action', 'crm_ai_send');
        fd.append('session', session);
        fd.append('message', text);
        fd.append('site_id', s.site_id);

        try {
            const res = await fetch(s.ajax_url, { method: 'POST', body: fd });
            const result = await res.json();
            if (result.success) {
                addMessage(result.message || 'Повідомлення надіслано', false);
            } else {
                addMessage('Помилка: ' + (result.message || ''), false);
            }
        } catch (e) {
            addMessage('Помилка зв\'язку', false);
        }
    }

    // Події
    openBtn.onclick = () => { 
        chat.style.display = 'flex'; 
        openBtn.style.display = 'none'; 
    };
    
    document.getElementById('closeBtn').onclick = () => { 
        chat.style.display = 'none'; 
        openBtn.style.display = 'block'; 
    };
    
    document.getElementById('sendBtn').onclick = sendMessage;
    inputField.addEventListener('keypress', e => { 
        if (e.key === 'Enter') sendMessage(); 
    });

})();
