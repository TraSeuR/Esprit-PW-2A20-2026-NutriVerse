// MENU SIDEBAR MOBILE
const menuBtn = document.getElementById("menuBtn");
const sidebar = document.getElementById("sidebar");

if (menuBtn && sidebar) {
  menuBtn.addEventListener("click", () => {
    sidebar.classList.toggle("active");
  });
}

// ANIMATION AU SCROLL
const faders = document.querySelectorAll(".fade-up");

const appearOnScroll = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.classList.add("show");
    }
  });
}, {
  threshold: 0.15
});

faders.forEach(fader => {
  appearOnScroll.observe(fader);
});
