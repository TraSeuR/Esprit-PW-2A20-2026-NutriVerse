<?php

?>


<button id="nutricoach-toggle" aria-label="Ouvrir le coach NutriVerse" title="Coach NutriVerse">
    <span class="coach-emoji-main">AI</span>
    <span class="coach-close-icon">✕</span>
    <span id="nutricoach-badge" class="hidden" aria-label="Nouveau message">1</span>
</button>


<div id="nutricoach-window" role="dialog" aria-label="Coach Virtuel NutriVerse" aria-modal="false">


    <div id="coach-header">
        <div class="coach-avatar">AI</div>
        <div class="coach-header-info">
            <h4>NutriCoach</h4>
            <span>
                <span class="coach-online-dot"></span>
                Coach Virtuel NutriVerse
            </span>
        </div>
        <button id="coach-clear-btn" title="Effacer la conversation">Effacer</button>
    </div>


    <div id="coach-messages" role="log" aria-live="polite" aria-label="Messages du coach">

    </div>


    <div id="coach-typing" class="hidden" aria-label="Le coach rédige une réponse">
        <div class="typing-avatar">AI</div>
        <div class="typing-bubble">
            <span class="typing-dot"></span>
            <span class="typing-dot"></span>
            <span class="typing-dot"></span>
        </div>
    </div>


    <div id="coach-suggestions" aria-label="Suggestions de questions">

    </div>


    <div id="coach-error" role="alert" aria-live="assertive"></div>


    <div id="coach-input-area">
        <input type="text" id="coach-input" placeholder="Posez votre question..." autocomplete="off" maxlength="1000"
            aria-label="Votre question pour le coach">
        <button id="coach-send-btn" aria-label="Envoyer" title="Envoyer">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z" />
            </svg>
        </button>
    </div>


    <div id="coach-footer">Propulsé par Google Gemini • NutriVerse 2026</div>

</div>


<link rel="stylesheet" href="assets/coach.css">
<script src="assets/coach.js" defer></script>