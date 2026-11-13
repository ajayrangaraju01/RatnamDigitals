document.addEventListener('DOMContentLoaded', function () {
    const lightbox = document.getElementById('portfolio-lightbox');
    if (!lightbox) {
        return;
    }

    const closeButton = document.querySelector('.portfolio-lightbox-close');
    const lightboxImage = document.getElementById('portfolio-lightbox-image');
    const lightboxTitle = document.getElementById('portfolio-lightbox-title');
    const lightboxCaption = document.getElementById('portfolio-lightbox-caption');

    const openLightbox = (button) => {
        const img = button.querySelector('img');
        const title = button.dataset.title || 'Ratnam Digitals';
        const caption = button.dataset.caption || 'Curated photography by Ratnam Digitals.';

        if (lightboxImage && img) {
            lightboxImage.src = img.src;
            lightboxImage.alt = title;
            lightboxImage.style.objectPosition = (button.dataset.cropX || '50') + '% ' + (button.dataset.cropY || '50') + '%';
        }

        if (lightboxTitle) {
            lightboxTitle.textContent = title;
        }

        if (lightboxCaption) {
            lightboxCaption.textContent = caption;
        }

        lightbox.classList.add('open');
        lightbox.setAttribute('aria-hidden', 'false');
        document.body.classList.add('modal-open');
    };

    const closeLightbox = () => {
        lightbox.classList.remove('open');
        lightbox.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('modal-open');
    };

    document.querySelectorAll('.portfolio-item').forEach((item) => {
        item.addEventListener('click', () => openLightbox(item));
    });

    if (closeButton) {
        closeButton.addEventListener('click', closeLightbox);
    }

    lightbox.addEventListener('click', (event) => {
        if (event.target === lightbox) {
            closeLightbox();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && lightbox.classList.contains('open')) {
            closeLightbox();
        }
    });
});
