<footer class="footer">
    <div class="container footer-content">
        <div>
            <h3>NutriVerse</h3>
            <p>Nutrition intelligente</p>
        </div>
        <div class="footer-links">
            <a href="#">Accueil</a> <a href="#">Recettes</a> <a href="#">Produits</a>
        </div>
    </div>
</footer>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const faders = document.querySelectorAll('.fade-up');
    const appearOnScroll = new IntersectionObserver(function(entries, observer) {
        entries.forEach(entry => {
            if (!entry.isIntersecting) {
                return;
            } else {
                entry.target.classList.add('show');
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.15
    });

    faders.forEach(fader => {
        appearOnScroll.observe(fader);
    });
});
</script>

</body>
</html>