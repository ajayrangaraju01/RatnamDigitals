document.addEventListener('DOMContentLoaded', function () {
    var track = document.querySelector('.hero-carousel-track');
    if (!track) return;

    var slides = track.querySelectorAll('.hero-carousel-slide');
    var prevBtn = document.querySelector('.hero-prev');
    var nextBtn = document.querySelector('.hero-next');
    var dotsContainer = document.querySelector('.hero-carousel-dots');
    var current = 0;
    var total = slides.length;
    var autoplay;

    function goTo(index) {
        if (index < 0) index = total - 1;
        if (index >= total) index = 0;
        current = index;
        track.style.transform = 'translateX(-' + (current * 100) + '%)';
        updateDots();
    }

    function updateDots() {
        if (!dotsContainer) return;
        dotsContainer.querySelectorAll('.hero-carousel-dot').forEach(function (dot, i) {
            dot.classList.toggle('active', i === current);
        });
    }

    function startAutoplay() {
        clearInterval(autoplay);
        autoplay = setInterval(function () { goTo(current + 1); }, 6000);
    }

    if (dotsContainer) {
        slides.forEach(function (_, i) {
            var dot = document.createElement('button');
            dot.className = 'hero-carousel-dot' + (i === 0 ? ' active' : '');
            dot.setAttribute('aria-label', 'Go to slide ' + (i + 1));
            dot.addEventListener('click', function () { goTo(i); });
            dotsContainer.appendChild(dot);
        });
    }

    if (prevBtn) prevBtn.addEventListener('click', function () { goTo(current - 1); });
    if (nextBtn) nextBtn.addEventListener('click', function () { goTo(current + 1); });

    startAutoplay();

    track.addEventListener('mouseenter', function () { clearInterval(autoplay); });
    track.addEventListener('mouseleave', function () { startAutoplay(); });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'ArrowLeft') goTo(current - 1);
        if (e.key === 'ArrowRight') goTo(current + 1);
    });
});
