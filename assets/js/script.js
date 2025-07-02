// Animasi scroll untuk navigasi
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
            behavior: 'smooth'
        });
    });
});

// Alert untuk fitur WhatsApp
document.querySelectorAll('.whatsapp-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        alert('Anda akan diarahkan ke WhatsApp untuk konsultasi dengan ahli pertanian kami.');
    });
});

// Fungsi untuk menghilangkan alert setelah beberapa detik
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            alert.style.transition = 'opacity 0.5s ease-out';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);
});