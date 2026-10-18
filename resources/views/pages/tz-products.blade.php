<x-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/tz.css') }}">
    @endpush

    <div class="tz-page">
        @include('pages.partials.tz-admin-sidebar')

        <main class="tz-inventory">
            <section class="tz-hero" aria-labelledby="inventory-heading">
                <div>
                    <span class="tz-eyebrow">TZ ATELIER &bull; TZ Carshop &bull; Vehicle Inventory</span>
                    <h1 id="inventory-heading">Choose Your Toyota</h1>
                    <p>Swipe to explore available vehicles</p>
                </div>
                <a href="{{ route('furever.products.create') }}" class="tz-button tz-button--primary">
                    <i class="fa fa-plus" aria-hidden="true"></i> Add New Car
                </a>
            </section>

            @if(session('success'))
                <div class="tz-alert tz-alert--success" role="status">
                    <i class="fa fa-check-circle" aria-hidden="true"></i> {{ session('success') }}
                </div>
            @endif

            <section class="tz-stats" aria-label="Inventory summary">
                <article><span>Vehicles</span><strong>{{ $totalProducts }}</strong></article>
                <article><span>In stock</span><strong>{{ $totalStock }}</strong></article>
                <article><span>Transmissions</span><strong data-total-transmissions>{{ $totalCategories }}</strong></article>
            </section>

            <form action="{{ route('furever.products') }}" method="GET" class="tz-search">
                <label for="vehicle-search" class="sr-only">Search vehicle inventory</label>
                <i class="fa fa-search" aria-hidden="true"></i>
                <input id="vehicle-search" type="search" name="search" value="{{ request('search') }}" placeholder="Search Toyota model, color, or fuel type">
                <button type="submit">Search</button>
            </form>

            @if($products->isNotEmpty())
                @php
                    $selected = $products->first();
                    $imageUrl = function ($product) {
                        if (! $product->image) return null;
                        return filter_var($product->image, FILTER_VALIDATE_URL)
                            ? $product->image
                            : asset('storage/'.$product->image);
                    };
                @endphp

                <section class="tz-carousel" data-car-carousel tabindex="0" aria-roledescription="carousel" aria-label="Toyota vehicle selector">
                    <button type="button" class="tz-carousel__arrow tz-carousel__arrow--previous" data-carousel-previous aria-label="Select previous car">
                        <i class="fa fa-chevron-left" aria-hidden="true"></i>
                    </button>

                    <div class="tz-carousel__viewport" data-carousel-viewport>
                        <div class="tz-carousel__track" data-carousel-track>
                            @foreach($products as $index => $product)
                                @php($carImage = $imageUrl($product))
                                <article
                                    class="tz-car-card {{ $loop->first ? 'is-selected' : '' }}"
                                    data-carousel-slide
                                    data-index="{{ $index }}"
                                    data-name="{{ $product->name }}"
                                    data-year="{{ $product->year }}"
                                    data-price="{{ number_format($product->price, 2) }}"
                                    data-color="{{ $product->color }}"
                                    data-transmission="{{ $product->transmission }}"
                                    data-fuel="{{ $product->fuel_type }}"
                                    data-stock="{{ $product->stock }}"
                                    data-availability="{{ $product->stock > 0 ? 'Available' : 'Out of stock' }}"
                                    data-description="{{ $product->description ?: 'No description provided.' }}"
                                    data-image="{{ $carImage }}"
                                    data-edit-url="{{ route('furever.products.edit', $product) }}"
                                    data-delete-url="{{ route('furever.products.destroy', $product) }}"
                                    aria-label="{{ $product->name }}, slide {{ $loop->iteration }} of {{ $products->count() }}"
                                >
                                    <button type="button" class="tz-car-card__select" data-select-car aria-label="Select {{ $product->name }}">
                                        <span class="tz-car-card__year">{{ $product->year }}</span>
                                        <span class="tz-car-card__image">
                                            @if($carImage)
                                                <img src="{{ $carImage }}" alt="{{ $product->name }}">
                                            @else
                                                <i class="fa fa-car" aria-hidden="true"></i>
                                            @endif
                                        </span>
                                        <strong>{{ $product->name }}</strong>
                                        <small>&#8369;{{ number_format($product->price, 2) }}</small>
                                    </button>
                                </article>
                            @endforeach
                        </div>
                    </div>

                    <button type="button" class="tz-carousel__arrow tz-carousel__arrow--next" data-carousel-next aria-label="Select next car">
                        <i class="fa fa-chevron-right" aria-hidden="true"></i>
                    </button>
                </section>

                <div class="tz-position" aria-live="polite">
                    <span data-carousel-position>1 / {{ $products->count() }}</span>
                    <div class="tz-dots" aria-hidden="true">
                        @foreach($products as $product)
                            <span class="{{ $loop->first ? 'is-active' : '' }}" data-carousel-dot></span>
                        @endforeach
                    </div>
                </div>

                <section class="tz-details" data-car-details aria-live="polite">
                    <div class="tz-details__heading">
                        <div><span class="tz-eyebrow">Selected vehicle</span><h2 data-detail-name>{{ $selected->name }}</h2></div>
                        <span class="tz-badge {{ $selected->stock > 0 ? 'is-available' : 'is-unavailable' }}" data-detail-availability>
                            {{ $selected->stock > 0 ? 'Available' : 'Out of stock' }}
                        </span>
                    </div>

                    <div class="tz-specs">
                        <div><span>Year</span><strong data-detail-year>{{ $selected->year }}</strong></div>
                        <div><span>Price</span><strong data-detail-price>&#8369;{{ number_format($selected->price, 2) }}</strong></div>
                        <div><span>Color</span><strong data-detail-color>{{ $selected->color }}</strong></div>
                        <div><span>Transmission</span><strong data-detail-transmission>{{ $selected->transmission }}</strong></div>
                        <div><span>Fuel type</span><strong data-detail-fuel>{{ $selected->fuel_type }}</strong></div>
                        <div><span>Stock</span><strong data-detail-stock>{{ $selected->stock }}</strong></div>
                    </div>

                    <div class="tz-details__actions">
                        <button type="button" class="tz-button tz-button--dark" data-open-details>View Details</button>
                        <a href="{{ route('furever.products.edit', $selected) }}" class="tz-button tz-button--outline" data-edit-car><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a>
                        <form action="{{ route('furever.products.destroy', $selected) }}" method="POST" data-delete-car>
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="tz-button tz-button--danger"><i class="fa fa-trash" aria-hidden="true"></i> Delete</button>
                        </form>
                    </div>
                </section>

                <div class="tz-modal" data-details-modal hidden>
                    <div class="tz-modal__backdrop" data-close-details></div>
                    <section class="tz-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="car-modal-title">
                        <button type="button" class="tz-modal__close" data-close-details aria-label="Close details">&times;</button>
                        <div class="tz-modal__image">
                            <img src="{{ $imageUrl($selected) }}" alt="" data-modal-image @if(!$imageUrl($selected)) hidden @endif>
                            <i class="fa fa-car" data-modal-placeholder aria-hidden="true" @if($imageUrl($selected)) hidden @endif></i>
                        </div>
                        <span class="tz-eyebrow">Vehicle details</span>
                        <h2 id="car-modal-title" data-modal-name>{{ $selected->name }}</h2>
                        <p data-modal-description>{{ $selected->description ?: 'No description provided.' }}</p>
                        <dl class="tz-modal__specs">
                            <div><dt>Year</dt><dd data-modal-year>{{ $selected->year }}</dd></div>
                            <div><dt>Price</dt><dd data-modal-price>&#8369;{{ number_format($selected->price, 2) }}</dd></div>
                            <div><dt>Color</dt><dd data-modal-color>{{ $selected->color }}</dd></div>
                            <div><dt>Transmission</dt><dd data-modal-transmission>{{ $selected->transmission }}</dd></div>
                            <div><dt>Fuel type</dt><dd data-modal-fuel>{{ $selected->fuel_type }}</dd></div>
                            <div><dt>Stock</dt><dd data-modal-stock>{{ $selected->stock }}</dd></div>
                        </dl>
                    </section>
                </div>
            @else
                <section class="tz-empty">
                    <i class="fa fa-car" aria-hidden="true"></i>
                    <h2>No Toyota vehicles yet</h2>
                    <p>Add the first vehicle to start your inventory.</p>
                    <a href="{{ route('furever.products.create') }}" class="tz-button tz-button--primary">Add New Car</a>
                </section>
            @endif
        </main>
    </div>

    @if($products->isNotEmpty())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var carousel = document.querySelector('[data-car-carousel]');
                if (!carousel) return;
                var viewport = carousel.querySelector('[data-carousel-viewport]');
                var slides = Array.from(carousel.querySelectorAll('[data-carousel-slide]'));
                var dots = Array.from(document.querySelectorAll('[data-carousel-dot]'));
                var position = document.querySelector('[data-carousel-position]');
                var details = document.querySelector('[data-car-details]');
                var editLink = details.querySelector('[data-edit-car]');
                var deleteForm = details.querySelector('[data-delete-car]');
                var modal = document.querySelector('[data-details-modal]');
                var current = 0;
                var pointerStart = null;
                var scrollStart = 0;
                var scrollTimer;

                function selectCar(index, shouldScroll) {
                    current = (index + slides.length) % slides.length;
                    var slide = slides[current];
                    slides.forEach(function (item, itemIndex) { item.classList.toggle('is-selected', itemIndex === current); });
                    dots.forEach(function (dot, dotIndex) { dot.classList.toggle('is-active', dotIndex === current); });
                    position.textContent = (current + 1) + ' / ' + slides.length;
                    details.classList.remove('is-updating');
                    void details.offsetWidth;
                    details.classList.add('is-updating');
                    details.querySelector('[data-detail-name]').textContent = slide.dataset.name;
                    details.querySelector('[data-detail-year]').textContent = slide.dataset.year;
                    details.querySelector('[data-detail-price]').textContent = '\u20b1' + slide.dataset.price;
                    details.querySelector('[data-detail-color]').textContent = slide.dataset.color;
                    details.querySelector('[data-detail-transmission]').textContent = slide.dataset.transmission;
                    details.querySelector('[data-detail-fuel]').textContent = slide.dataset.fuel;
                    details.querySelector('[data-detail-stock]').textContent = slide.dataset.stock;
                    var badge = details.querySelector('[data-detail-availability]');
                    badge.textContent = slide.dataset.availability;
                    badge.classList.toggle('is-available', Number(slide.dataset.stock) > 0);
                    badge.classList.toggle('is-unavailable', Number(slide.dataset.stock) <= 0);
                    editLink.href = slide.dataset.editUrl;
                    deleteForm.action = slide.dataset.deleteUrl;
                    if (shouldScroll !== false) {
                        viewport.scrollTo({
                            left: slide.offsetLeft - (viewport.clientWidth - slide.offsetWidth) / 2,
                            behavior: 'smooth'
                        });
                    }
                }

                function closestSlide() {
                    var viewportCenter = viewport.getBoundingClientRect().left + viewport.clientWidth / 2;
                    var bestIndex = 0;
                    var bestDistance = Infinity;
                    slides.forEach(function (slide, index) {
                        var rect = slide.getBoundingClientRect();
                        var distance = Math.abs(rect.left + rect.width / 2 - viewportCenter);
                        if (distance < bestDistance) { bestDistance = distance; bestIndex = index; }
                    });
                    selectCar(bestIndex, false);
                }

                carousel.querySelector('[data-carousel-previous]').addEventListener('click', function () { selectCar(current - 1); });
                carousel.querySelector('[data-carousel-next]').addEventListener('click', function () { selectCar(current + 1); });
                slides.forEach(function (slide, index) { slide.querySelector('[data-select-car]').addEventListener('click', function () { selectCar(index); }); });
                carousel.addEventListener('keydown', function (event) {
                    if (event.key === 'ArrowLeft' || event.key === 'ArrowRight') {
                        event.preventDefault();
                        selectCar(current + (event.key === 'ArrowRight' ? 1 : -1));
                    }
                });
                viewport.addEventListener('scroll', function () {
                    window.clearTimeout(scrollTimer);
                    scrollTimer = window.setTimeout(closestSlide, 100);
                }, { passive: true });
                viewport.addEventListener('pointerdown', function (event) {
                    pointerStart = event.clientX;
                    scrollStart = viewport.scrollLeft;
                    viewport.setPointerCapture(event.pointerId);
                    viewport.classList.add('is-dragging');
                });
                viewport.addEventListener('pointermove', function (event) {
                    if (pointerStart !== null) viewport.scrollLeft = scrollStart - (event.clientX - pointerStart);
                });
                viewport.addEventListener('pointerup', function () {
                    pointerStart = null;
                    viewport.classList.remove('is-dragging');
                    closestSlide();
                });
                viewport.addEventListener('pointercancel', function () { pointerStart = null; viewport.classList.remove('is-dragging'); });
                deleteForm.addEventListener('submit', function (event) {
                    if (!window.confirm('Delete ' + slides[current].dataset.name + ' from the inventory?')) event.preventDefault();
                });

                function closeModal() { modal.hidden = true; document.body.classList.remove('tz-modal-open'); }
                document.querySelector('[data-open-details]').addEventListener('click', function () {
                    var slide = slides[current];
                    modal.querySelector('[data-modal-name]').textContent = slide.dataset.name;
                    modal.querySelector('[data-modal-description]').textContent = slide.dataset.description;
                    modal.querySelector('[data-modal-year]').textContent = slide.dataset.year;
                    modal.querySelector('[data-modal-price]').textContent = '\u20b1' + slide.dataset.price;
                    modal.querySelector('[data-modal-color]').textContent = slide.dataset.color;
                    modal.querySelector('[data-modal-transmission]').textContent = slide.dataset.transmission;
                    modal.querySelector('[data-modal-fuel]').textContent = slide.dataset.fuel;
                    modal.querySelector('[data-modal-stock]').textContent = slide.dataset.stock;
                    var image = modal.querySelector('[data-modal-image]');
                    var placeholder = modal.querySelector('[data-modal-placeholder]');
                    image.hidden = !slide.dataset.image;
                    placeholder.hidden = Boolean(slide.dataset.image);
                    if (slide.dataset.image) image.src = slide.dataset.image;
                    modal.hidden = false;
                    document.body.classList.add('tz-modal-open');
                    modal.querySelector('.tz-modal__close').focus();
                });
                modal.querySelectorAll('[data-close-details]').forEach(function (button) { button.addEventListener('click', closeModal); });
                document.addEventListener('keydown', function (event) { if (event.key === 'Escape' && !modal.hidden) closeModal(); });
                window.requestAnimationFrame(function () {
                    selectCar(slides.length > 2 ? 1 : 0);
                });
            });
        </script>
    @endif
</x-layout>
