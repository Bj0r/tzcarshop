/*
 * Scroll-driven hero film for the TZ Carshop landing page.
 *
 * The video was cut into 120 still frames (public/img/hero-frames). As the
 * customer scrolls through the tall #scene-1 section, the frame that matches
 * the scroll position is drawn on a <canvas>, so the car moves with the scroll
 * and plays backwards when scrolling up.
 */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        var section = document.querySelector('[data-hero-scrub]');

        if (!section) {
            return;
        }

        var stage = section.querySelector('.hero-scrub__stage');
        var canvas = section.querySelector('[data-hero-canvas]');
        var poster = section.querySelector('[data-hero-poster]');
        var intro = section.querySelector('[data-hero-intro]');
        var outro = section.querySelector('[data-hero-outro]');
        var bar = section.querySelector('[data-hero-progress]');
        var count = parseInt(section.dataset.frameCount, 10) || 0;

        var isSmallScreen = window.matchMedia('(max-width: 768px)').matches;
        var folder = isSmallScreen ? section.dataset.framesMobile : section.dataset.framesDesktop;

        // The film plays during the first 80% of the scroll, then holds on the last frame.
        var FILM_END = 0.8;

        function frameUrl(index) {
            return folder + '/frame_' + String(index + 1).padStart(3, '0') + '.webp';
        }

        function clamp(value) {
            return Math.max(0, Math.min(1, value));
        }

        // Less motion requested, or no canvas support: show the final frame as a still image.
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches || !canvas || !canvas.getContext || !count) {
            if (poster && count) {
                poster.src = frameUrl(count - 1);
            }

            if (outro) {
                outro.classList.add('is-visible');
            }

            return;
        }

        var context = canvas.getContext('2d');
        var frames = new Array(count);
        var loaded = new Array(count);
        var drawnIndex = -1;
        var targetIndex = 0;
        var ticking = false;

        /* ---- Loading: first frame, then every 8th, then the rest, so scrolling early still looks smooth. ---- */
        var order = [0];

        for (var step = 8; step < count; step += 8) {
            order.push(step);
        }

        for (var i = 1; i < count; i++) {
            if (order.indexOf(i) === -1) {
                order.push(i);
            }
        }

        function loadFrame(index) {
            var image = new Image();
            image.decoding = 'async';
            image.onload = function () {
                loaded[index] = true;

                if (drawnIndex === -1 || Math.abs(index - targetIndex) < Math.abs(drawnIndex - targetIndex)) {
                    draw(targetIndex);
                }
            };
            image.src = frameUrl(index);
            frames[index] = image;
        }

        // The 15 key frames load now; the other 105 wait until the rest of the page has loaded,
        // so they never slow down the page's own images and scripts.
        var keyFrameCount = Math.ceil(count / 8);

        order.slice(0, keyFrameCount).forEach(loadFrame);

        function loadRemainingFrames() {
            order.slice(keyFrameCount).forEach(loadFrame);
        }

        if (document.readyState === 'complete') {
            loadRemainingFrames();
        } else {
            window.addEventListener('load', loadRemainingFrames, { once: true });
        }

        /* ---- Drawing ---- */
        function resizeCanvas() {
            var ratio = Math.min(window.devicePixelRatio || 1, 2);
            canvas.width = Math.round(canvas.clientWidth * ratio);
            canvas.height = Math.round(canvas.clientHeight * ratio);
            drawnIndex = -1;
            draw(targetIndex);
        }

        // Use the wanted frame, or the closest frame that has finished loading.
        function nearestLoaded(index) {
            for (var distance = 0; distance < count; distance++) {
                if (loaded[index - distance]) {
                    return index - distance;
                }

                if (loaded[index + distance]) {
                    return index + distance;
                }
            }

            return -1;
        }

        function draw(index) {
            var available = nearestLoaded(index);

            if (available === -1 || available === drawnIndex) {
                return;
            }

            var image = frames[available];
            var scale = Math.max(canvas.width / image.naturalWidth, canvas.height / image.naturalHeight);
            var width = image.naturalWidth * scale;
            var height = image.naturalHeight * scale;

            // "Cover" fit, like object-fit: cover.
            context.drawImage(image, (canvas.width - width) / 2, (canvas.height - height) / 2, width, height);
            drawnIndex = available;
            section.classList.add('is-ready');
        }

        /* ---- Scroll ---- */
        function update() {
            ticking = false;

            var rect = section.getBoundingClientRect();
            var travel = section.offsetHeight - window.innerHeight;
            var progress = travel > 0 ? clamp(-rect.top / travel) : 0;
            var filmProgress = clamp(progress / FILM_END);

            targetIndex = Math.round(filmProgress * (count - 1));
            draw(targetIndex);

            // Opening text fades out quickly, before the headlights come on.
            if (intro) {
                var introHide = clamp(progress / 0.12);
                intro.style.opacity = String(1 - introHide);
                intro.style.transform = 'translateY(' + (-30 * introHide) + 'px)';
            }

            // Car name and price fade in as the full car is revealed.
            if (outro) {
                var outroShow = clamp((progress - 0.62) / 0.15);
                outro.style.opacity = String(outroShow);
                outro.style.transform = 'translateY(' + (24 * (1 - outroShow)) + 'px)';
                outro.classList.toggle('is-visible', outroShow > 0.5);
            }

            // Fade the whole stage at the very end, into the next scene.
            stage.style.setProperty('--hero-fade', String(1 - clamp((progress - 0.9) / 0.1)));

            if (bar) {
                bar.style.transform = 'scaleX(' + filmProgress + ')';
            }
        }

        function requestUpdate() {
            if (!ticking) {
                ticking = true;
                window.requestAnimationFrame(update);
            }
        }

        window.addEventListener('scroll', requestUpdate, { passive: true });
        window.addEventListener('resize', function () {
            resizeCanvas();
            requestUpdate();
        });

        resizeCanvas();
        update();
    });
})();
