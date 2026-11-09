document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('input[type="file"][data-image-crop]').forEach(function (input) {
        const form = input.closest('form');
        const aspectRatio = Number.parseFloat(input.dataset.imageCrop);
        const serviceCropXField = form.querySelector('input[name="service_crop_x"]');
        const serviceCropYField = form.querySelector('input[name="service_crop_y"]');
        const editor = document.createElement('div');
        editor.className = 'upload-crop-editor';
        editor.hidden = true;
        editor.style.setProperty('--crop-aspect', String(aspectRatio));

        const preview = document.createElement('div');
        preview.className = 'upload-crop-preview';
        const previewCanvas = document.createElement('canvas');
        preview.appendChild(previewCanvas);
        editor.appendChild(preview);

        const controls = document.createElement('div');
        controls.className = 'upload-crop-controls';
        editor.appendChild(controls);

        const zoom = addControl(controls, 'Zoom', 100, 300, 100, '%');
        const horizontal = addControl(controls, 'Horizontal position', 0, 100, 50, '%');
        const vertical = addControl(controls, 'Vertical position', 0, 100, 50, '%');
        const errorMessage = document.createElement('p');
        errorMessage.className = 'upload-crop-error';
        errorMessage.hidden = true;
        editor.appendChild(errorMessage);
        input.insertAdjacentElement('afterend', editor);

        let currentImage = null;
        let currentFile = null;
        let processedFile = null;
        let imageUrl = null;
        let processing = false;

        function addControl(container, label, min, max, value, suffix) {
            const wrapper = document.createElement('label');
            wrapper.className = 'upload-crop-control';
            const name = document.createElement('span');
            name.textContent = label;
            const range = document.createElement('input');
            range.type = 'range';
            range.min = String(min);
            range.max = String(max);
            range.value = String(value);
            range.step = '1';
            const output = document.createElement('output');
            output.value = String(value) + suffix;
            wrapper.append(name, range, output);
            container.appendChild(wrapper);
            range.addEventListener('input', function () {
                output.value = range.value + suffix;
                if (serviceCropXField && label === 'Horizontal position') {
                    serviceCropXField.value = range.value;
                }
                if (serviceCropYField && label === 'Vertical position') {
                    serviceCropYField.value = range.value;
                }
                drawPreview();
            });
            return range;
        }

        function cropBounds() {
            const imageWidth = currentImage.naturalWidth;
            const imageHeight = currentImage.naturalHeight;
            let width = imageWidth;
            let height = width / aspectRatio;
            if (height > imageHeight) {
                height = imageHeight;
                width = height * aspectRatio;
            }

            const scale = Number(zoom.value) / 100;
            width /= scale;
            height /= scale;
            return {
                x: (imageWidth - width) * Number(horizontal.value) / 100,
                y: (imageHeight - height) * Number(vertical.value) / 100,
                width: width,
                height: height,
            };
        }

        function drawCrop(canvas, width, height) {
            const bounds = cropBounds();
            canvas.width = width;
            canvas.height = height;
            canvas.getContext('2d').drawImage(
                currentImage,
                bounds.x,
                bounds.y,
                bounds.width,
                bounds.height,
                0,
                0,
                width,
                height
            );
            return bounds;
        }

        function drawPreview() {
            if (!currentImage) return;
            const width = 1200;
            drawCrop(previewCanvas, width, Math.round(width / aspectRatio));
        }

        input.addEventListener('change', function () {
            if (imageUrl) URL.revokeObjectURL(imageUrl);
            currentImage = null;
            currentFile = null;
            processedFile = null;
            editor.hidden = true;
            errorMessage.hidden = true;

            const file = input.files && input.files[0];
            if (!file || !file.type.startsWith('image/')) return;

            currentFile = file;
            zoom.value = '100';
            horizontal.value = '50';
            vertical.value = '50';
            [zoom, horizontal, vertical].forEach(function (range) {
                range.dispatchEvent(new Event('input'));
            });

            imageUrl = URL.createObjectURL(file);
            const image = new Image();
            image.onload = function () {
                currentImage = image;
                editor.hidden = false;
                drawPreview();
                URL.revokeObjectURL(imageUrl);
                imageUrl = null;
            };
            image.onerror = function () {
                errorMessage.textContent = 'This image could not be opened for cropping.';
                errorMessage.hidden = false;
            };
            image.src = imageUrl;
        });

        form.addEventListener('submit', async function (event) {
            const file = input.files && input.files[0];
            if (!file || !file.type.startsWith('image/') || file === processedFile) return;

            event.preventDefault();
            if (processing) return;
            processing = true;
            errorMessage.hidden = true;
            const submitter = event.submitter;
            if (submitter) submitter.disabled = true;

            try {
                if (!currentImage || file !== currentFile) {
                    throw new Error('Wait for the crop preview to finish loading, then try again.');
                }

                const bounds = cropBounds();
                const canvas = document.createElement('canvas');
                const outputWidth = Math.max(1, Math.round(bounds.width));
                const outputHeight = Math.max(1, Math.round(bounds.height));
                drawCrop(canvas, outputWidth, outputHeight);
                const outputType = ['image/jpeg', 'image/png', 'image/webp'].includes(file.type) ? file.type : 'image/jpeg';
                const blob = await new Promise(function (resolve, reject) {
                    canvas.toBlob(function (result) {
                        if (result) resolve(result);
                        else reject(new Error('The cropped image could not be created.'));
                    }, outputType, 0.92);
                });

                const extension = blob.type === 'image/png' ? 'png' : blob.type === 'image/webp' ? 'webp' : 'jpg';
                const baseName = file.name.replace(/\.[^.]+$/, '') || 'image';
                processedFile = new File([blob], baseName + '_cropped.' + extension, { type: blob.type });
                const transfer = new DataTransfer();
                transfer.items.add(processedFile);
                input.files = transfer.files;
                if (submitter) submitter.disabled = false;
                processing = false;
                form.requestSubmit(submitter || undefined);
            } catch (error) {
                errorMessage.textContent = error.message || 'Image cropping failed. Please try again.';
                errorMessage.hidden = false;
                if (submitter) submitter.disabled = false;
                processing = false;
            }
        });
    });
});