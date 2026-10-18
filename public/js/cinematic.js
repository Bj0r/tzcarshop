/**
 * Cinematic UI Design System — Runtime Engine
 * Manages scene scroll progress, parallax, spec reveals, finish selection, and ordering workflow
 */

(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', initCinematicExperience);

    function initCinematicExperience() {
        initScrollProgress();
        initParallax();
        initSpecReveal();
        initColourSelector();
        initCockpitTelemetry();
        initReservationWorkflow();
        initSmoothNav();
    }

    /* --------------------------------------------------------------------------
       1. Scroll-Driven Scene Transitions
       -------------------------------------------------------------------------- */
    function initScrollProgress() {
        const wrappers = document.querySelectorAll('.scene-wrapper');
        if (!wrappers.length) return;

        function updateSceneProgress() {
            const viewportHeight = window.innerHeight;

            wrappers.forEach((wrapper) => {
                const rect = wrapper.getBoundingClientRect();
                const totalTravel = wrapper.offsetHeight - viewportHeight;
                let progress = 0;

                if (totalTravel > 0) {
                    progress = Math.max(0, Math.min(1, -rect.top / totalTravel));
                }

                const scene = wrapper.querySelector('.scene');
                if (scene) {
                    scene.style.setProperty('--progress', progress);
                }
            });

            requestAnimationFrame(updateSceneProgress);
        }

        requestAnimationFrame(updateSceneProgress);
    }

    /* --------------------------------------------------------------------------
       2. Layered Parallax (Anchored text, moving atmosphere)
       -------------------------------------------------------------------------- */
    function initParallax() {
        const layers = document.querySelectorAll('[data-parallax]');
        if (!layers.length) return;

        let ticking = false;

        window.addEventListener('scroll', () => {
            if (!ticking) {
                window.requestAnimationFrame(() => {
                    const scrollY = window.scrollY;

                    layers.forEach((layer) => {
                        const speed = parseFloat(layer.dataset.parallax) || 0.3;
                        const wrapper = layer.closest('.scene-wrapper');
                        if (!wrapper) return;

                        const rect = wrapper.getBoundingClientRect();
                        // Only calculate if within or close to viewport
                        if (rect.top < window.innerHeight && rect.bottom > 0) {
                            const offset = (window.innerHeight - rect.top) * speed * 0.25;
                            layer.style.transform = `translate3d(0, ${offset}px, 0)`;
                        }
                    });

                    ticking = false;
                });
                ticking = true;
            }
        }, { passive: true });
    }

    /* --------------------------------------------------------------------------
       3. Horizontal Spec Reveal Animation
       -------------------------------------------------------------------------- */
    function initSpecReveal() {
        const specFills = document.querySelectorAll('.spec-bar-fill');
        if (!specFills.length) return;

        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('in-view');
                    }
                });
            }, { threshold: 0.25 });

            specFills.forEach((fill) => observer.observe(fill));
        } else {
            specFills.forEach((fill) => fill.classList.add('in-view'));
        }
    }

    /* --------------------------------------------------------------------------
       4. Horizontal Colour Selector & Vehicle Preview Synchronization
       -------------------------------------------------------------------------- */
    function initColourSelector() {
        const cards = document.querySelectorAll('[data-colour-card]');
        const previewImage = document.querySelector('[data-colour-preview-image]');
        const previewName = document.querySelector('[data-selected-finish-name]');
        const previewCode = document.querySelector('[data-selected-finish-code]');
        const modalColorInput = document.querySelector('#res-finish-name');
        const modalColorSummary = document.querySelector('#modal-summary-finish');

        cards.forEach((card) => {
            card.addEventListener('click', () => {
                cards.forEach((c) => c.classList.remove('is-active'));
                card.classList.add('is-active');

                const name = card.dataset.finishName;
                const code = card.dataset.finishCode;

                if (previewName) previewName.textContent = name;
                if (previewCode) previewCode.textContent = code;
                if (modalColorInput) modalColorInput.value = name;
                if (modalColorSummary) modalColorSummary.textContent = name;

                // Subtle hue tint on vehicle silhouette for preview immersion
                if (previewImage) {
                    const tint = card.dataset.finishFilter || 'none';
                    previewImage.style.filter = tint;
                }
            });
        });
    }

    /* --------------------------------------------------------------------------
       5. Cockpit HUD Live Telemetry Simulator
       -------------------------------------------------------------------------- */
    function initCockpitTelemetry() {
        const speedEl = document.querySelector('.hud-speed');
        if (!speedEl) return;

        let baseSpeed = 128;
        let direction = 1;

        setInterval(() => {
            baseSpeed += direction * (Math.floor(Math.random() * 3) - 1);
            if (baseSpeed > 136) direction = -1;
            if (baseSpeed < 122) direction = 1;

            if (speedEl) {
                speedEl.textContent = baseSpeed;
            }
        }, 1200);
    }

    /* --------------------------------------------------------------------------
       6. Bespoke Reservation & Order Flow (UI/UX Principles)
       -------------------------------------------------------------------------- */
    function initReservationWorkflow() {
        const modal = document.querySelector('#reservation-modal');
        const openButtons = document.querySelectorAll('[data-open-reservation]');
        const closeButtons = document.querySelectorAll('[data-close-reservation]');
        const vehicleSelect = document.querySelector('#res-vehicle-select');
        const modalPrice = document.querySelector('#modal-summary-price');
        const modalCarImage = document.querySelector('#modal-summary-image');
        const modalCarName = document.querySelector('#modal-summary-name');
        const modalCarYear = document.querySelector('#modal-summary-year');
        const modalCarTransmission = document.querySelector('#modal-summary-trans');
        const form = document.querySelector('#cinematic-reservation-form');
        const formSection = document.querySelector('#reservation-form-section');
        const successSection = document.querySelector('#reservation-success-section');

        function openModal() {
            if (!modal) return;
            modal.classList.add('is-open');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            if (!modal) return;
            modal.classList.remove('is-open');
            document.body.style.overflow = '';
        }

        openButtons.forEach((btn) => btn.addEventListener('click', (e) => {
            e.preventDefault();
            const carId = btn.dataset.carId;
            if (carId && vehicleSelect) {
                vehicleSelect.value = carId;
                updateVehicleDetails(carId);
            }
            openModal();
        }));

        closeButtons.forEach((btn) => btn.addEventListener('click', closeModal));

        if (modal) {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) closeModal();
            });
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && modal && modal.classList.contains('is-open')) {
                closeModal();
            }
        });

        // Vehicle dropdown sync
        function updateVehicleDetails(productId) {
            if (!vehicleSelect) return;
            const selectedOpt = vehicleSelect.querySelector(`option[value="${productId}"]`) || vehicleSelect.selectedOptions[0];
            if (!selectedOpt) return;

            const price = selectedOpt.dataset.priceFormatted || '₱0.00';
            const name = selectedOpt.dataset.name || '';
            const year = selectedOpt.dataset.year || '';
            const trans = selectedOpt.dataset.transmission || '';
            const img = selectedOpt.dataset.image || '';

            if (modalPrice) modalPrice.textContent = price;
            if (modalCarName) modalCarName.textContent = name;
            if (modalCarYear) modalCarYear.textContent = year;
            if (modalCarTransmission) modalCarTransmission.textContent = trans;
            if (modalCarImage && img) modalCarImage.src = img;
        }

        if (vehicleSelect) {
            vehicleSelect.addEventListener('change', () => {
                updateVehicleDetails(vehicleSelect.value);
            });
            // Initial call
            updateVehicleDetails(vehicleSelect.value);
        }

        // Reservation Form Submission (Ajax for smooth experience)
        if (form) {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalText = submitBtn ? submitBtn.textContent : 'Confirm';

                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'COMMISSIONING...';
                }

                const formData = new FormData(form);

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        // Success screen
                        if (formSection) formSection.style.display = 'none';
                        if (successSection) {
                            successSection.style.display = 'block';
                            const refCode = successSection.querySelector('[data-order-ref]');
                            const carConfirmed = successSection.querySelector('[data-confirmed-car]');
                            if (refCode) refCode.textContent = 'ORD-' + String(data.order_id).padStart(5, '0');
                            if (carConfirmed) carConfirmed.textContent = data.product_name || 'Toyota Commissioned Vehicle';
                        }
                    } else {
                        alert(data.message || 'Unable to place reservation. Please verify your details.');
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.textContent = originalText;
                        }
                    }
                } catch (err) {
                    // Fallback to standard form post if json fails
                    form.submit();
                }
            });
        }
    }

    /* --------------------------------------------------------------------------
       7. Smooth Anchored Nav Jump
       -------------------------------------------------------------------------- */
    function initSmoothNav() {
        document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
            anchor.addEventListener('click', function (e) {
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;

                const targetElem = document.querySelector(targetId);
                if (targetElem) {
                    e.preventDefault();
                    targetElem.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
    }
})();
