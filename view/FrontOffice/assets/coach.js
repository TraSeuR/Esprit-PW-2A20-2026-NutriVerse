

(function () {
    'use strict';


    const COACH_ENDPOINT = (function () {
        const depth = window.location.pathname.split('/').filter(Boolean).length;

        return '../../controller/CoachController.php';
    })();

    const MAX_CHARS = 1000;


    const SUGGESTIONS_MAP = {
        'nutri_front': [' Quel régime pour moi ?', ' Protéines journalières', ' Perdre du poids'],
        'mode_selection': [' Comment créer mon régime ?', ' Planning sportif', ' Quel programme choisir ?'],
        'add_regime': [' Calories recommandées', ' Glucides vs lipides', ' Type de régime'],
        'add_planning': [' Sport par semaine', ' Sommeil optimal', ' Programme débutant'],
        'summary': [' Analyser mon bilan', ' Améliorer mon régime', ' Progresser'],
        'edit_regime': [' Changer mon type de régime', ' Modifier mes calories', ' Aide modification'],
        'edit_programme_front': [' Adapter mon planning', ' Revoir mes séances', ' Conseils'],
        'view_ready_plannings': [' Quel planning choisir ?', ' Comprendre un planning', ' Régime associé'],
        'list_programmes': [' Voir mes programmes', ' Modifier un régime', ' Supprimer un programme'],
        'default': [' Conseil nutrition', ' Conseil sport', ' Sommeil & récupération']
    };


    const toggleBtn = document.getElementById('nutricoach-toggle');
    const chatWindow = document.getElementById('nutricoach-window');
    const messagesBox = document.getElementById('coach-messages');
    const inputField = document.getElementById('coach-input');
    const sendBtn = document.getElementById('coach-send-btn');
    const typingEl = document.getElementById('coach-typing');
    const suggestEl = document.getElementById('coach-suggestions');
    const errorEl = document.getElementById('coach-error');
    const clearBtn = document.getElementById('coach-clear-btn');
    const badge = document.getElementById('nutricoach-badge');

    if (!toggleBtn || !chatWindow) return;


    let isOpen = false;
    let isLoading = false;
    let hasNewMsg = true;


    function init() {

        const pageName = detectCurrentPage();


        const history = loadHistory();

        if (history.length > 0) {
            history.forEach(msg => renderMessage(msg.text, msg.type, msg.time, false));
        } else {

            const welcome = getWelcomeMessage(pageName);
            renderMessage(welcome, 'bot', now(), false);
            saveToHistory(welcome, 'bot');
        }


        renderSuggestions(pageName);


        toggleBtn.addEventListener('click', toggleChat);
        sendBtn.addEventListener('click', sendMessage);
        clearBtn.addEventListener('click', clearChat);

        inputField.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        inputField.addEventListener('input', function () {
            hideError();
            // Limiter visuellement la saisie
            if (this.value.length > MAX_CHARS) {
                this.value = this.value.substring(0, MAX_CHARS);
            }
        });


        setTimeout(() => {
            if (!isOpen && hasNewMsg) showBadge();
        }, 2000);
    }


    function toggleChat() {
        isOpen = !isOpen;

        if (isOpen) {
            chatWindow.classList.add('is-open');
            toggleBtn.classList.add('is-open');
            hideBadge();
            setTimeout(() => {
                inputField.focus();
                scrollToBottom();
            }, 320);
        } else {
            chatWindow.classList.remove('is-open');
            toggleBtn.classList.remove('is-open');
        }
    }


    function sendMessage() {

        const text = inputField.value.trim();

        if (!text) {
            showError('Veuillez écrire un message avant d\'envoyer.');
            return;
        }

        if (text.length > MAX_CHARS) {
            showError('Message trop long (maximum ' + MAX_CHARS + ' caractères).');
            return;
        }

        if (isLoading) return;

        hideError();
        hideSuggestions();


        const timestamp = now();
        renderMessage(text, 'user', timestamp, true);
        saveToHistory(text, 'user');
        inputField.value = '';


        fetchCoachReply(text);
    }

    /* ─────────────────────────────────────────
       APPEL API (fetch → CoachController.php)
    ───────────────────────────────────────── */
    function fetchCoachReply(userMessage) {
        isLoading = true;
        sendBtn.disabled = true;
        showTyping();
        scrollToBottom();

        fetch(COACH_ENDPOINT, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'NutriCoachRequest'
            },
            body: JSON.stringify({ message: userMessage })
        })
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                hideTyping();

                if (data.error) {
                    renderMessage(' ' + data.error, 'bot', now(), true);
                } else {
                    const reply = data.reply || 'Désolé, je n\'ai pas pu générer une réponse.';
                    renderMessage(reply, 'bot', now(), true);
                    saveToHistory(reply, 'bot');

                    // Afficher de nouvelles suggestions après la réponse
                    setTimeout(() => {
                        renderSuggestions(detectCurrentPage());
                    }, 400);
                }
            })
            .catch(function (err) {
                hideTyping();
                renderMessage(
                    '🔌 Impossible de contacter le coach. Vérifiez que XAMPP est démarré et que votre clé API est configurée dans CoachController.php.',
                    'bot', now(), true
                );
                console.error('[NutriCoach] Erreur fetch :', err);
            })
            .finally(function () {
                isLoading = false;
                sendBtn.disabled = false;
                scrollToBottom();
            });
    }


    function renderMessage(text, type, timestamp, animate) {
        const wrapper = document.createElement('div');
        wrapper.classList.add('coach-msg', type);
        if (!animate) wrapper.style.animation = 'none';

        if (type === 'bot') {
            wrapper.innerHTML =
                '<div class="msg-avatar">AI</div>' +
                '<div>' +
                '<div class="msg-bubble">' + formatText(text) + '</div>' +
                '<span class="msg-time">' + timestamp + '</span>' +
                '</div>';
        } else {
            wrapper.innerHTML =
                '<div>' +
                '<div class="msg-bubble">' + escapeHtml(text) + '</div>' +
                '<span class="msg-time">' + timestamp + '</span>' +
                '</div>';
        }

        messagesBox.appendChild(wrapper);

        if (animate) scrollToBottom();
    }

    /* ─────────────────────────────────────────
       SUGGESTIONS RAPIDES
    ───────────────────────────────────────── */
    function renderSuggestions(pageName) {
        suggestEl.innerHTML = '';
        const suggestions = SUGGESTIONS_MAP[pageName] || SUGGESTIONS_MAP['default'];

        suggestions.forEach(function (text) {
            const btn = document.createElement('button');
            btn.className = 'coach-suggestion-btn';
            btn.textContent = text;
            btn.addEventListener('click', function () {
                inputField.value = text.replace(/^[\p{Emoji}\s]+/u, '').trim();
                hideSuggestions();
                sendMessage();
            });
            suggestEl.appendChild(btn);
        });

        suggestEl.classList.remove('hidden');
    }

    function hideSuggestions() {
        suggestEl.classList.add('hidden');
    }


    function showTyping() {
        typingEl.classList.remove('hidden');
        scrollToBottom();
    }

    function hideTyping() {
        typingEl.classList.add('hidden');
    }

    /* ─────────────────────────────────────────
       EFFACER L'HISTORIQUE
    ───────────────────────────────────────── */
    function clearChat() {
        messagesBox.innerHTML = '';
        sessionStorage.removeItem('nutricoach_history');
        const welcome = getWelcomeMessage(detectCurrentPage());
        renderMessage(welcome, 'bot', now(), true);
        saveToHistory(welcome, 'bot');
        renderSuggestions(detectCurrentPage());
    }


    function showBadge() {
        badge.classList.remove('hidden');
    }

    function hideBadge() {
        badge.classList.add('hidden');
        hasNewMsg = false;
    }

    /* ─────────────────────────────────────────
       ERREURS
    ───────────────────────────────────────── */
    function showError(msg) {
        errorEl.textContent = msg;
        errorEl.classList.add('visible');
        inputField.focus();
    }

    function hideError() {
        errorEl.classList.remove('visible');
    }


    function saveToHistory(text, type) {
        const history = loadHistory();
        history.push({ text: text, type: type, time: now() });

        const trimmed = history.slice(-20);
        try {
            sessionStorage.setItem('nutricoach_history', JSON.stringify(trimmed));
        } catch (e) { /* Ignorer si sessionStorage indisponible */ }
    }

    function loadHistory() {
        try {
            return JSON.parse(sessionStorage.getItem('nutricoach_history') || '[]');
        } catch (e) {
            return [];
        }
    }


    function scrollToBottom() {
        setTimeout(function () {
            messagesBox.scrollTop = messagesBox.scrollHeight;
        }, 60);
    }

    function now() {
        return new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
    }

    function escapeHtml(str) {
        return str
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }


    function formatText(text) {
        return escapeHtml(text)
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.*?)\*/g, '<em>$1</em>')
            .replace(/\n- /g, '\n• ')
            .replace(/\n/g, '<br>');
    }

    function detectCurrentPage() {
        const path = window.location.pathname;
        const file = path.split('/').pop().replace('.php', '');
        return file || 'default';
    }

    function getWelcomeMessage(pageName) {
        const messages = {
            'nutri_front': 'Bonjour !  Je suis **NutriCoach**, votre assistant NutriVerse. Posez-moi vos questions sur la nutrition, les régimes ou vos programmes !',
            'mode_selection': ' Besoin d\'aide pour choisir entre un régime manuel ou un planning expert ? Je suis là pour vous guider !',
            'add_regime': ' Vous créez votre régime alimentaire ! Besoin de conseils sur les calories, les macros ou le type de régime ? Demandez-moi !',
            'add_planning': ' Parfait ! Vous planifiez vos séances. Je peux vous conseiller sur la fréquence d\'entraînement et le sommeil optimal.',
            'summary': ' Voici votre bilan NutriVerse ! Des questions sur vos résultats ou comment optimiser votre programme ?',
            'edit_regime': ' Vous modifiez votre régime. Besoin d\'aide pour ajuster vos objectifs nutritionnels ?',
            'view_ready_plannings': ' Parcourez nos plannings experts ! Je peux vous aider à choisir celui qui correspond à vos objectifs.',
            'list_programmes': ' Voici vos programmes. Des questions sur vos données ou comment améliorer votre suivi ?',
            'default': 'Bonjour !  Je suis **NutriCoach**, votre coach virtuel NutriVerse. Comment puis-je vous aider aujourd\'hui ?'
        };
        return messages[pageName] || messages['default'];
    }


    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
