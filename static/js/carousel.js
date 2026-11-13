document.addEventListener('DOMContentLoaded', function () {
    const carousels = document.querySelectorAll('.carousel, .hero-carousel');

    carousels.forEach(function (carousel) {
        const isHero = carousel.classList.contains('hero-carousel');
        const track = carousel.querySelector(isHero ? '.hero-carousel-track' : '.carousel-track');
        const slides = track ? track.querySelectorAll(isHero ? '.hero-carousel-slide' : '.carousel-slide') : [];
        const prevBtn = carousel.querySelector(isHero ? '.hero-prev' : '.carousel-btn.prev');
        const nextBtn = carousel.querySelector(isHero ? '.hero-next' : '.carousel-btn.next');
        const dotsContainer = carousel.querySelector(isHero ? '.hero-carousel-dots' : '.carousel-dots');
        let current = 0;
        const total = slides.length;
        let autoplay;

        if (!track || total === 0) {
            return;
        }

        function updateDots() {
            if (!dotsContainer) return;
            const dotSelector = isHero ? '.hero-carousel-dot' : '.carousel-dot';
            dotsContainer.querySelectorAll(dotSelector).forEach(function (dot, index) {
                dot.classList.toggle('active', index === current);
            });
        }

        function goTo(index) {
            if (index < 0) index = total - 1;
            if (index >= total) index = 0;
            current = index;
            track.style.transform = 'translateX(-' + (current * 100) + '%)';
            updateDots();
        }

        if (dotsContainer) {
            const dotClass = isHero ? 'hero-carousel-dot' : 'carousel-dot';
            slides.forEach(function (_, index) {
                var dot = document.createElement('button');
                dot.type = 'button';
                dot.className = dotClass + (index === 0 ? ' active' : '');
                dot.setAttribute('aria-label', isHero ? 'Go to slide ' + (index + 1) : 'Go to photo ' + (index + 1));
                dot.addEventListener('click', function () { goTo(index); });
                dotsContainer.appendChild(dot);
            });
        }

        if (total === 1) {
            updateDots();
            return;
        }

        if (prevBtn) prevBtn.addEventListener('click', function () { goTo(current - 1); });
        if (nextBtn) nextBtn.addEventListener('click', function () { goTo(current + 1); });

        function startAutoplay() {
            clearInterval(autoplay);
            autoplay = setInterval(function () { goTo(current + 1); }, isHero ? 6000 : 5000);
        }

        startAutoplay();

        if (isHero) {
            carousel.addEventListener('mouseenter', function () { clearInterval(autoplay); });
            carousel.addEventListener('mouseleave', function () { startAutoplay(); });
        }

        carousel.addEventListener('keydown', function (event) {
            if (event.key === 'ArrowLeft') goTo(current - 1);
            if (event.key === 'ArrowRight') goTo(current + 1);
        });
    });
});
