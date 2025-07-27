{{-- Just here as a binder for dynamically rendered content. --}}
<style>
  #ai-trigger {
    position: fixed;
    bottom: 20px;
    right: 20px;
    width: 55px;
    height: 55px;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(12px);
    border-radius: 50%;
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 0 10px rgba(0, 123, 255, 0.5);
    z-index: 9999;
    display: flex;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    transition: all 0.3s ease;
  }
  #ai-trigger:hover {
    transform: scale(1.1);
    box-shadow: 0 0 15px rgba(0, 123, 255, 0.8);
  }
  #ai-chatbox {
    position: fixed;
    bottom: 90px;
    right: 20px;
    width: 320px;
    height: 420px;
    background: rgba(30, 30, 30, 0.6);
    backdrop-filter: blur(16px);
    border-radius: 16px;
    box-shadow: 0 0 30px rgba(0, 0, 0, 0.4);
    padding: 16px;
    color: white;
    display: none;
    flex-direction: column;
    justify-content: space-between;
    z-index: 9999;
    animation: fadeInUp 0.5s ease-out;
  }
  @keyframes fadeInUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
  }
  #ai-header {
    font-weight: bold;
    font-size: 16px;
    margin-bottom: 8px;
    border-bottom: 1px solid rgba(255,255,255,0.2);
    padding-bottom: 6px;
  }
  #ai-messages {
    flex: 1;
    overflow-y: auto;
    margin-bottom: 10px;
    font-size: 14px;
  }
  .ai-message {
    margin-bottom: 10px;
    line-height: 1.4;
    animation: fadeInUp 0.4s ease;
  }
  #ai-input {
    display: flex;
    align-items: center;
    gap: 8px;
  }
  #ai-input textarea {
    flex: 1;
    resize: none;
    border: none;
    border-radius: 10px;
    padding: 8px 12px;
    background: rgba(255, 255, 255, 0.1);
    color: white;
    font-size: 14px;
    backdrop-filter: blur(5px);
    max-height: 80px;
    overflow-y: auto;
  }
  #ai-input button {
    background: #0d6efd;
    color: white;
    border: none;
    padding: 8px 12px;
    border-radius: 10px;
    cursor: pointer;
    transition: 0.3s;
  }
  #ai-input button:hover {
    background: #0b5ed7;
  }
</style>

<div id="ai-trigger">
  <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10
    10-4.48 10-10S17.52 2 12 2zm-1 17.07c-.76-.29-1.44-.75-2.01-1.32a7.007 7.007
    0 0 1-1.32-2.01C7.27 14.28 7 13.16 7 12s.27-2.28.67-3.24c.29-.76.75-1.44
    1.32-2.01.57-.57 1.25-1.03 2.01-1.32C10.72 5.27 11.84 5 13 5s2.28.27
    3.24.67c.76.29 1.44.75 2.01 1.32.57.57 1.03 1.25 1.32 2.01.4.96.67 2.08.67
    3.24s-.27 2.28-.67 3.24c-.29.76-.75 1.44-1.32 2.01-.57.57-1.25 1.03-2.01
    1.32C15.72 18.73 14.6 19 13.45 19c-1.15 0-2.27-.27-3.24-.67z" fill="#0d6efd"/>
  </svg>
</div>

<div id="ai-chatbox">
  <div id="ai-header">Asisten AI <span style="font-weight:normal">(by Rin Dev)</span></div>
  <div id="ai-messages"></div>
  <div id="ai-input">
    <textarea id="ai-text" rows="1" placeholder="Tulis pertanyaan..."></textarea>
    <button id="ai-send">Kirim</button>
  </div>
</div>

<script>
  const aiTrigger = document.getElementById('ai-trigger');
  const aiChatbox = document.getElementById('ai-chatbox');
  const aiMessages = document.getElementById('ai-messages');
  const aiText = document.getElementById('ai-text');
  const aiSend = document.getElementById('ai-send');

  aiTrigger.addEventListener('click', () => {
    aiChatbox.style.display = aiChatbox.style.display === 'flex' ? 'none' : 'flex';
  });

  aiText.addEventListener('keydown', e => {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault();
      aiSend.click();
    }
  });

  aiSend.addEventListener('click', async () => {
    const input = aiText.value.trim();
    if (!input) return;
    aiMessages.innerHTML += `<div class='ai-message'><b>Kamu:</b> ${input}</div>`;
    aiText.value = '';

    const res = await fetch('https://api.together.xyz/v1/chat/completions', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': 'Bearer Your api key'
      },
      body: JSON.stringify({
        model: 'meta-llama/Llama-3-8b-chat-hf',
        messages: [
          {
            role: 'system',
            content: 'Kamu adalah asisten AI berbahasa Indonesia, buatan Rin Dev. Jawab semua topik dengan sopan, informatif, dan ramah.'
          },
          {
            role: 'user',
            content: input
          }
        ]
      })
    });

    const data = await res.json();
    const reply = data.choices?.[0]?.message?.content || 'Maaf, tidak ada balasan.';
    aiMessages.innerHTML += `<div class='ai-message'><b>AI:</b> ${reply}</div>`;
    aiMessages.scrollTop = aiMessages.scrollHeight;
  });
</script>
