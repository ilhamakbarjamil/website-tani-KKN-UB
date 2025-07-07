document.addEventListener('DOMContentLoaded', function() {

    // 1. Hamburger Menu Toggle
    const hamburger = document.querySelector('.hamburger');
    const navMenu = document.querySelector('.nav-menu');

    hamburger.addEventListener('click', () => {
        hamburger.classList.toggle('active');
        navMenu.classList.toggle('active');
    });

    // Menutup menu saat link di-klik
    document.querySelectorAll('.nav-link').forEach(n => n.addEventListener('click', () => {
        hamburger.classList.remove('active');
        navMenu.classList.remove('active');
    }));
    
    // 2. Smooth Scrolling
    // (CSS `scroll-behavior: smooth;` sudah menangani ini untuk klik link #, 
    // tapi ini adalah cara JS alternatif jika diperlukan)
    // Dibiarkan simpel dengan CSS

    // 3. Fade-in on Scroll Animation
    const faders = document.querySelectorAll('.fade-in, .fade-in-left, .fade-in-right');

    const appearOptions = {
        threshold: 0.2, // Elemen akan muncul saat 20% terlihat
        rootMargin: "0px 0px -50px 0px" // Sedikit delay sebelum trigger
    };

    const appearOnScroll = new IntersectionObserver(function(entries, appearOnScroll) {
        entries.forEach(entry => {
            if (!entry.isIntersecting) {
                return;
            } else {
                entry.target.classList.add('is-visible');
                appearOnScroll.unobserve(entry.target);
            }
        });
    }, appearOptions);

    faders.forEach(fader => {
        appearOnScroll.observe(fader);
    });

});