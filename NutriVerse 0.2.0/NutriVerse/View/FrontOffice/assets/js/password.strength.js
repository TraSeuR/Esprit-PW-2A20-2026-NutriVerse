/**
 * password.strength.js
 * ─────────────────────────────────────────────────────
 * Password strength meter + secure password generator
 * Targets:
 *   #mot_de_passe        — password input
 *   #pw-strength-bar-wrap — wrapper to show/hide
 *   #pw-strength-fill    — colored fill bar
 *   #pw-strength-label   — text label
 *   #pw-rules            — optional rule list (ul)
 *   #rule-len / #rule-upper / #rule-num / #rule-special
 *   #btn-gen-pw          — generate button
 *   #toggle-pw1          — show/hide toggle for main pw
 *   #toggle-pw2          — show/hide toggle for confirm
 *   #confirm_mot_de_passe / #confirm_pw — confirm input
 *   #match-msg           — match feedback paragraph
 */
(function () {
    'use strict';

    // ── Strength scoring ──────────────────────────────────────
    function scorePassword(pw) {
        let score = 0;
        const checks = {
            len: pw.length >= 8,
            upper: /[A-Z]/.test(pw),
            num: /[0-9]/.test(pw),
            special: /[\W_]/.test(pw),
            long: pw.length >= 12,
        };
        if (checks.len) score++;
        if (checks.upper) score++;
        if (checks.num) score++;
        if (checks.special) score++;
        if (checks.long) score++;
        return { score, checks };
    }

    const levels = [
        { label: 'Très faible', color: '#ef4444', pct: '15%' },
        { label: 'Faible', color: '#f97316', pct: '30%' },
        { label: 'Moyen', color: '#eab308', pct: '55%' },
        { label: 'Bien', color: '#84cc16', pct: '75%' },
        { label: 'Excellent', color: '#16a34a', pct: '100%' },
    ];

    function updateRuleItem(id, passed) {
        const el = document.getElementById(id);
        if (!el) return;
        el.style.color = passed ? '#16a34a' : '#9ca3af';
        el.style.fontWeight = passed ? '600' : '400';
        el.textContent = (passed ? '✓ ' : '○ ') + el.textContent.replace(/^[✓○] /, '');
    }

    // ── Generate secure password ───────────────────────────────
    function generatePassword(len = 16) {
        const upper = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        const lower = 'abcdefghjkmnpqrstuvwxyz';
        const numbers = '23456789';
        const special = '!@#$%^&*-_+=?';
        const all = upper + lower + numbers + special;
        const arr = new Uint8Array(len);
        crypto.getRandomValues(arr);
        let pw = upper[arr[0] % upper.length]
            + numbers[arr[1] % numbers.length]
            + special[arr[2] % special.length]
            + lower[arr[3] % lower.length];
        for (let i = 4; i < len; i++) {
            pw += all[arr[i] % all.length];
        }
        // Shuffle
        return pw.split('').sort(() => Math.random() - 0.5).join('');
    }

    // ── Wire up ───────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', () => {
        const pwInput = document.getElementById('mot_de_passe');
        const barWrap = document.getElementById('pw-strength-bar-wrap');
        const fill = document.getElementById('pw-strength-fill');
        const label = document.getElementById('pw-strength-label');
        const rulesEl = document.getElementById('pw-rules');
        const genBtn = document.getElementById('btn-gen-pw');
        const toggle1 = document.getElementById('toggle-pw1');
        const toggle2 = document.getElementById('toggle-pw2');
        const confirm = document.getElementById('confirm_mot_de_passe') || document.getElementById('confirm_pw');
        const matchMsg = document.getElementById('match-msg');

        if (!pwInput) return;

        // ── Strength bar ──────────────────────────────────────
        pwInput.addEventListener('input', () => {
            const pw = pwInput.value;
            if (!pw) {
                if (barWrap) barWrap.style.display = 'none';
                if (rulesEl) rulesEl.style.display = 'none';
                return;
            }
            if (barWrap) barWrap.style.display = 'block';
            if (rulesEl) rulesEl.style.display = 'block';

            const { score, checks } = scorePassword(pw);
            const lvl = levels[Math.min(score, levels.length - 1)];

            if (fill) { fill.style.width = lvl.pct; fill.style.background = lvl.color; }
            if (label) { label.textContent = lvl.label; label.style.color = lvl.color; }

            updateRuleItem('rule-len', checks.len);
            updateRuleItem('rule-upper', checks.upper);
            updateRuleItem('rule-num', checks.num);
            updateRuleItem('rule-special', checks.special);

            // Confirm match check
            if (confirm && confirm.value) checkMatch();
        });

        // ── Confirm match ─────────────────────────────────────
        function checkMatch() {
            if (!confirm || !matchMsg) return;
            const match = pwInput.value === confirm.value;
            matchMsg.style.display = 'block';
            matchMsg.textContent = match ? '✅ Les mots de passe correspondent.' : '❌ Les mots de passe ne correspondent pas.';
            matchMsg.style.color = match ? '#16a34a' : '#ef4444';
        }
        if (confirm) confirm.addEventListener('input', checkMatch);

        // ── Password generator ────────────────────────────────
        if (genBtn) {
            genBtn.addEventListener('click', () => {
                const pw = generatePassword(16);
                pwInput.value = pw;
                pwInput.type = 'text';
                pwInput.dispatchEvent(new Event('input'));
                if (confirm) {
                    confirm.value = pw;
                    confirm.dispatchEvent(new Event('input'));
                }
                setTimeout(() => { pwInput.type = 'password'; }, 3000);
            });
        }

        // ── Toggle visibility (main) ──────────────────────────
        if (toggle1) {
            toggle1.addEventListener('click', () => {
                pwInput.type = pwInput.type === 'password' ? 'text' : 'password';
                toggle1.textContent = pwInput.type === 'password' ? '👁️' : '🙈';
            });
        }

        // ── Toggle visibility (confirm) ───────────────────────
        const toggleBtn2 = document.getElementById('toggle-pw2');
        if (toggleBtn2 && confirm) {
            toggleBtn2.addEventListener('click', () => {
                confirm.type = confirm.type === 'password' ? 'text' : 'password';
                toggleBtn2.textContent = confirm.type === 'password' ? '👁️' : '🙈';
            });
        }
    });
})();
