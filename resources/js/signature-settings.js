import { removeBackground } from '@imgly/background-removal';

const root = document.querySelector('[data-signature-settings]');

if (root) {
    const uploadForm = root.querySelector('#signature-upload-form');
    const source = root.querySelector('#signature-source');
    const processed = root.querySelector('#signature-processed');
    const preview = root.querySelector('#signature-preview');
    const previewWrap = root.querySelector('#signature-preview-wrap');
    const status = root.querySelector('#signature-status');
    const uploadButton = root.querySelector('#signature-submit');
    const drawForm = root.querySelector('#draw-signature-form');
    const drawCanvas = root.querySelector('#signature-pad');
    const drawFile = root.querySelector('#draw-signature-file');
    const drawPreview = root.querySelector('#draw-signature-preview');
    const drawPreviewWrap = root.querySelector('#draw-preview-wrap');
    const drawStatus = root.querySelector('#draw-signature-status');
    const drawButton = root.querySelector('#draw-signature-submit');

    let uploadPreviewUrl = null;
    let drawingPreviewUrl = null;

    const setStatus = (element, message, isError = false) => {
        element.textContent = message;
        element.classList.remove('hidden');
        element.style.color = isError ? 'var(--sg-danger)' : 'var(--sg-text-muted)';
    };

    const releasePreview = (url) => {
        if (url) URL.revokeObjectURL(url);
    };

    source?.addEventListener('change', async () => {
        processed.value = '';
        uploadButton.disabled = true;
        previewWrap.classList.add('hidden');
        status.classList.add('hidden');
        releasePreview(uploadPreviewUrl);
        uploadPreviewUrl = null;

        const file = source.files?.[0];
        if (!file) return;

        const allowedType = ['image/png', 'image/jpeg'].includes(file.type);
        const allowedExtension = /\.(png|jpe?g)$/i.test(file.name);
        if (!allowedType || !allowedExtension) {
            setStatus(status, 'Choose a JPG, JPEG, or PNG image.', true);
            return;
        }
        if (file.size > 10 * 1024 * 1024) {
            setStatus(status, 'The original image must be 10 MB or smaller.', true);
            return;
        }

        uploadButton.disabled = true;
        setStatus(status, 'Removing the background on this device. The first run may download the model files.');

        try {
            const blob = await removeBackground(file, {
                model: 'isnet_quint8',
                publicPath: 'https://staticimgly.com/@imgly/background-removal-data/1.7.0/dist/',
                output: { format: 'image/png' },
                progress: (key, current, total) => {
                    if (total > 0) {
                        setStatus(status, `Loading background-removal model: ${Math.round((current / total) * 100)}%`);
                    } else {
                        setStatus(status, 'Processing image locally. Please keep this page open.');
                    }
                },
            });

            if (!blob || blob.type !== 'image/png') {
                throw new Error('The image model did not produce a PNG. Please try another image.');
            }
            if (blob.size > 5 * 1024 * 1024) {
                throw new Error('The processed signature is larger than 5 MB. Choose a smaller image.');
            }

            const cleanedFile = new File([blob], 'official-signature.png', { type: 'image/png' });
            const transfer = new DataTransfer();
            transfer.items.add(cleanedFile);
            processed.files = transfer.files;
            uploadPreviewUrl = URL.createObjectURL(blob);
            preview.src = uploadPreviewUrl;
            previewWrap.classList.remove('hidden');
            uploadButton.disabled = false;
            setStatus(status, 'Background removed locally. Review the transparent preview before saving.');
        } catch (error) {
            setStatus(status, error instanceof Error ? error.message : 'Background removal failed. Please try another image.', true);
        }
    });

    uploadForm?.addEventListener('submit', (event) => {
        if (!processed.files?.length) {
            event.preventDefault();
            setStatus(status, 'Select an image and wait for background removal before saving.', true);
        }
    });

    if (drawCanvas && drawFile && drawForm) {
        const context = drawCanvas.getContext('2d');
        if (!context) {
            setStatus(drawStatus, 'This browser cannot create a signature drawing.', true);
            drawButton.disabled = true;
        } else {
            let isDrawing = false;
            let hasDrawing = false;

            const resizeCanvas = () => {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                const bounds = drawCanvas.getBoundingClientRect();
                drawCanvas.width = Math.round(bounds.width * ratio);
                drawCanvas.height = Math.round(bounds.height * ratio);
                context.setTransform(ratio, 0, 0, ratio, 0, 0);
                context.lineWidth = 2.5;
                context.lineCap = 'round';
                context.lineJoin = 'round';
                context.strokeStyle = '#102a4c';
                if (hasDrawing) {
                    hasDrawing = false;
                    drawFile.value = '';
                    drawButton.disabled = true;
                    drawPreviewWrap.classList.add('hidden');
                    drawStatus.classList.add('hidden');
                    releasePreview(drawingPreviewUrl);
                    drawingPreviewUrl = null;
                }
            };

            const pointFor = (event) => {
                const bounds = drawCanvas.getBoundingClientRect();
                return { x: event.clientX - bounds.left, y: event.clientY - bounds.top };
            };

            const saveDrawingPreview = () => {
                drawCanvas.toBlob((blob) => {
                    if (!blob) {
                        drawButton.disabled = true;
                        setStatus(drawStatus, 'Could not create the transparent signature PNG.', true);
                        return;
                    }
                    if (blob.size > 5 * 1024 * 1024) {
                        drawButton.disabled = true;
                        setStatus(drawStatus, 'The drawing is larger than 5 MB. Clear it and draw a smaller signature.', true);
                        return;
                    }

                    const file = new File([blob], 'official-signature.png', { type: 'image/png' });
                    const transfer = new DataTransfer();
                    transfer.items.add(file);
                    drawFile.files = transfer.files;
                    releasePreview(drawingPreviewUrl);
                    drawingPreviewUrl = URL.createObjectURL(blob);
                    drawPreview.src = drawingPreviewUrl;
                    drawPreviewWrap.classList.remove('hidden');
                    drawButton.disabled = false;
                    setStatus(drawStatus, 'Transparent signature preview ready.');
                }, 'image/png');
            };

            resizeCanvas();
            window.addEventListener('resize', resizeCanvas);

            drawCanvas.addEventListener('pointerdown', (event) => {
                event.preventDefault();
                isDrawing = true;
                hasDrawing = true;
                drawCanvas.setPointerCapture(event.pointerId);
                const point = pointFor(event);
                context.beginPath();
                context.moveTo(point.x, point.y);
                context.lineTo(point.x + 0.01, point.y + 0.01);
                context.stroke();
                drawButton.disabled = true;
            });

            drawCanvas.addEventListener('pointermove', (event) => {
                if (!isDrawing) return;
                event.preventDefault();
                const point = pointFor(event);
                context.lineTo(point.x, point.y);
                context.stroke();
            });

            const finishStroke = (event) => {
                if (!isDrawing) return;
                isDrawing = false;
                if (drawCanvas.hasPointerCapture(event.pointerId)) {
                    drawCanvas.releasePointerCapture(event.pointerId);
                }
                if (hasDrawing) saveDrawingPreview();
            };

            drawCanvas.addEventListener('pointerup', finishStroke);
            drawCanvas.addEventListener('pointercancel', finishStroke);
            drawCanvas.addEventListener('lostpointercapture', () => {
                if (isDrawing) {
                    isDrawing = false;
                    if (hasDrawing) saveDrawingPreview();
                }
            });

            root.querySelector('#signature-clear')?.addEventListener('click', () => {
                context.setTransform(1, 0, 0, 1, 0, 0);
                context.clearRect(0, 0, drawCanvas.width, drawCanvas.height);
                resizeCanvas();
                hasDrawing = false;
                drawFile.value = '';
                drawButton.disabled = true;
                drawPreviewWrap.classList.add('hidden');
                drawStatus.classList.add('hidden');
                releasePreview(drawingPreviewUrl);
                drawingPreviewUrl = null;
            });

            drawForm.addEventListener('submit', (event) => {
                if (!drawFile.files?.length) {
                    event.preventDefault();
                    setStatus(drawStatus, 'Draw a signature and review the preview before saving.', true);
                }
            });
        }
    }
}
