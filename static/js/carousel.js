document.addEventListener('DOMContentLoaded', function () {
    const track = document.querySelector('.carousel-track');
    if (!track) return;

    const slides = track.querySelectorAll('.carousel-slide');
    const prevBtn = document.querySelector('.carousel-btn.prev');
    const nextBtn = document.querySelector('.carousel-btn.next');
    const dotsContainer = document.querySelector('.carousel-dots');
    let current = 0;
    const total = slides.length;

    function goTo(index) {
        if (index < 0) index = total - 1;
        if (index >= total) index = 0;
        current = index;
        track.style.transform = 'translateX(-' + (current * 100) + '%)';
        updateDots();
    }

    function updateDots() {
        dotsContainer.querySelectorAll('.carousel-dot').forEach(function (dot, i) {
            dot.classList.toggle('active', i === current);
        });
    }

    // Create dots
    slides.forEach(function (_, i) {
        var dot = document.createElement('button');
        dot.className = 'carousel-dot' + (i === 0 ? ' active' : '');
        dot.setAttribute('aria-label', 'Go to slide ' + (i + 1));
        dot.addEventListener('click', function () { goTo(i); });
        dotsContainer.appendChild(dot);
    });

    prevBtn.addEventListener('click', function () { goTo(current - 1); });
    nextBtn.addEventListener('click', function () { goTo(current + 1); });

    // Auto-advance every 5 seconds
    var autoplay = setInterval(function () { goTo(current + 1); }, 5000);

    track.addEventListener('mouseenter', function () { clearInterval(autoplay); });
    track.addEventListener('mouseleave', function () {
        autoplay = setInterval(function () { goTo(current + 1); }, 5000);
    });

    // Keyboard navigation
    document.addEventListener('keydown', function (e) {
        if (e.key === 'ArrowLeft') goTo(current - 1);
        if (e.key === 'ArrowRight') goTo(current + 1);
    });
});
