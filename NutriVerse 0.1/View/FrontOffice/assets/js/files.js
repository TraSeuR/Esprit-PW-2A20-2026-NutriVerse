function makePwToggle(btnId, fieldId) {
    const btn = document.getElementById(btnId);
    const field = document.getElementById(fieldId);
    if (!btn || !field) return;
    btn.addEventListener('click', function () {
        const hidden = field.type === 'password';
        field.type = hidden ? 'text' : 'password';
        btn.textContent = hidden ? '🙈' : '👁️';
    });
}

// For login.php
makePwToggle('toggle-pw-btn', 'mot_de_passe');

// For register.php
makePwToggle('toggle-pw1', 'mot_de_passe');
makePwToggle('toggle-pw2', 'confirm_mot_de_passe');
