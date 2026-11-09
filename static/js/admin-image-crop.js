document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.admin-crop-form').forEach(function (form) {
        var card = form.closest('.admin-image-card');
        var preview = card.querySelector('.admin-crop-preview img');
        var horizontal = form.querySelector('[name="crop_x"]');
        var vertical = form.querySelector('[name="crop_y"]');

        function updatePreview() {
            preview.style.objectPosition = horizontal.value + '% ' + vertical.value + '%';
        }

        horizontal.addEventListener('input', updatePreview);
        vertical.addEventListener('input', updatePreview);
    });
});