const chatbotRoot = document.querySelector('[data-chatbot]');

if (chatbotRoot) {
    const isLoggedIn = chatbotRoot.dataset.loggedIn === '1';
    const toggleBtn = chatbotRoot.querySelector('[data-chatbot-toggle]');
    const panel = chatbotRoot.querySelector('[data-chatbot-panel]');
    const closeBtn = chatbotRoot.querySelector('[data-chatbot-close]');
    const messagesList = chatbotRoot.querySelector('[data-chatbot-messages]');
    const emptyState = chatbotRoot.querySelector('[data-chatbot-empty]');
    const form = chatbotRoot.querySelector('[data-chatbot-form]');
    const input = chatbotRoot.querySelector('[data-chatbot-input]');
    const sendBtn = chatbotRoot.querySelector('[data-chatbot-send]');
    const lockedOverlay = chatbotRoot.querySelector('[data-chatbot-locked]');

    let historyLoaded = false;
    let isSending = false;

    const escapeHtml = (value = '') => {
        const stringValue = String(value ?? '');
        const escapeMap = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;',
        };
        return stringValue.replace(/[&<>"']/g, (char) => escapeMap[char]);
    };

    const formatTime = (value) => {
        const date = value ? new Date(value) : new Date();
        if (Number.isNaN(date.getTime())) {
            return '';
        }
        return date.toLocaleTimeString([], {hour: '2-digit', minute: '2-digit'});
    };

    const updateEmptyState = () => {
        if (!emptyState) {
            return;
        }
        const hasMessages = messagesList?.querySelector('.chatbot-message');
        emptyState.hidden = Boolean(hasMessages);
    };

    const scrollToBottom = () => {
        requestAnimationFrame(() => {
            messagesList.scrollTo({top: messagesList.scrollHeight, behavior: 'smooth'});
        });
    };

    const buildMessageElement = (sender, message, timestamp, isTyping = false) => {
        const wrapper = document.createElement('div');
        wrapper.className = `chatbot-message chatbot-message-${sender}${isTyping ? ' chatbot-message-typing' : ''}`;
        wrapper.innerHTML = `
            <div class="chatbot-bubble">
                ${isTyping ? '<span class="typing-dot"></span><span class="typing-dot"></span><span class="typing-dot"></span>' : escapeHtml(message)}
            </div>
            <span class="chatbot-time">${isTyping ? 'Typing…' : escapeHtml(formatTime(timestamp))}</span>
        `;
        return wrapper;
    };

    const appendMessage = (sender, message, timestamp) => {
        const element = buildMessageElement(sender, message, timestamp);
        messagesList.appendChild(element);
        updateEmptyState();
        scrollToBottom();
        return element;
    };

    const showTyping = () => {
        const element = buildMessageElement('bot', '', new Date(), true);
        messagesList.appendChild(element);
        scrollToBottom();
        return element;
    };

    const setFormDisabled = (disabled) => {
        if (!form || !input || !sendBtn) {
            return;
        }
        input.disabled = disabled;
        sendBtn.disabled = disabled;
    };

    const renderHistory = (messages) => {
        if (!Array.isArray(messages) || messages.length === 0) {
            return;
        }
        messages.forEach((item) => {
            const sender = item.sender === 'user' ? 'user' : 'bot';
            appendMessage(sender, item.message, item.created_at);
        });
    };

    const fetchHistory = async () => {
        if (!isLoggedIn || historyLoaded) {
            return;
        }
        historyLoaded = true;
        try {
            if (!window.NearBy?.fetchJSON) {
                throw new Error('Chat service is not available right now.');
            }
            const response = await fetch('api/message-assistant-history.php', {headers: {'Accept': 'application/json'}});
            if (!response.ok) {
                throw new Error('Unable to load chat history');
            }
            const data = await response.json();
            if (!data.success) {
                throw new Error(data.message || 'Unable to load chat history');
            }
            renderHistory(data.messages || []);
        } catch (error) {
            historyLoaded = false;
            if (window.NearBy?.showMessage) {
                window.NearBy.showMessage(error.message || 'Unable to load chat history', 'danger');
            } else {
                console.error(error);
            }
        }
    };

    const togglePanel = (forceState) => {
        const shouldOpen = typeof forceState === 'boolean' ? forceState : panel.hidden;
        panel.hidden = !shouldOpen;
        chatbotRoot.classList.toggle('chatbot-open', shouldOpen);
        if (shouldOpen) {
            fetchHistory();
            if (isLoggedIn && input) {
                setTimeout(() => input.focus(), 150);
            }
        }
    };

    if (toggleBtn) {
        toggleBtn.addEventListener('click', () => togglePanel());
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', () => togglePanel(false));
    }

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && chatbotRoot.classList.contains('chatbot-open')) {
            togglePanel(false);
        }
    });

    if (!isLoggedIn) {
        setFormDisabled(true);
        if (lockedOverlay) {
            lockedOverlay.removeAttribute('hidden');
        }
    } else if (form && input && sendBtn) {
        setFormDisabled(false);
        if (lockedOverlay) {
            lockedOverlay.setAttribute('hidden', 'hidden');
        }

        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            if (isSending) {
                return;
            }

            const rawValue = input.value.trim();
            if (!rawValue) {
                return;
            }

            isSending = true;
            setFormDisabled(true);

            appendMessage('user', rawValue, new Date());
            input.value = '';
            const typingIndicator = showTyping();

            try {
                const response = await fetch('api/message-assistant-send.php', {
                    method: 'POST',
                    headers: {'Accept': 'application/json', 'Content-Type': 'application/json'},
                    body: JSON.stringify({message: rawValue}),
                });
                let data;
                try {
                    data = await response.json();
                } catch {
                    throw new Error('Server returned invalid response');
                }
                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Unable to send message');
                }
                typingIndicator.remove();
                appendMessage('bot', data.reply, data.created_at || new Date());
            } catch (error) {
                typingIndicator.remove();
                const errorMessage = error.message.includes('Server returned invalid response') || error.message.includes('Failed to fetch') ? 'Sorry, I\'m having trouble connecting. Please try again later.' : error.message || 'Sorry, I\'m having trouble responding right now. Please try again later.';
                appendMessage('bot', errorMessage, new Date());
            } finally {
                isSending = false;
                setFormDisabled(false);
                if (input) {
                    input.focus();
                }
            }
        });
    }
}
