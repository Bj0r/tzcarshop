<x-layout>
    <x-slot:title>TZ ATELIER — The Vehicle Collection &amp; Inventory</x-slot:title>

    @include('pages.partials.tz-nav')

    @php($isAdmin = auth()->check() && auth()->user()->isFureverAdmin())

    <main class="cinematic-subpage-wrap">
        <div class="cinematic-subpage-container">
            @php($sessionFlash = session('success') ?? session('error'))
            <div id="shop-flash" class="cinematic-panel" style="padding: 16px 20px; margin-bottom: 24px;{{ $sessionFlash ? (session('success') ? ' border-color: rgba(45, 138, 94, 0.4); background: rgba(45, 138, 94, 0.1);' : ' border-color: rgba(196, 77, 90, 0.4); background: rgba(196, 77, 90, 0.1);') : '' }}" role="status" @unless($sessionFlash) hidden @endunless>
                @if($sessionFlash)
                    <span style="color: {{ session('success') ? '#4ade80' : '#f87171' }}; font-size: 13px; font-weight: 600;">
                        <i class="fa {{ session('success') ? 'fa-check-circle' : 'fa-exclamation-circle' }}"></i> {{ $sessionFlash }}
                    </span>
                @endif
            </div>

            <header class="cinematic-subpage-header">
                <div>
                    <span class="cinematic-subpage-eyebrow">TZ ATELIER &bull; Toyota Curation &bull; TZ Carshop</span>
                    <h1 class="cinematic-subpage-title">Choose Your Toyota</h1>
                    <p class="cinematic-subpage-subtitle">Handcrafted automotive engineering. Precision grand tourers, hybrid executive sedans, and performance chassis available for acquisition.</p>
                </div>
                <div style="display: flex; gap: 12px; align-items: center;">
                    <span class="nav-badge-link" style="font-size: 12px;">
                        <strong id="product-count" style="color: var(--accent-gold);">0</strong> <span id="product-count-label">Vehicles</span>
                    </span>
                    @if($isAdmin)
                        <a href="{{ route('furever.products.create') }}" class="cinematic-btn cinematic-btn--gold" style="font-size: 11px;">
                            <i class="fa fa-plus"></i> Add Car
                        </a>
                    @endif
                </div>
            </header>

            {{-- FILTER STRIP (results come from GET /api/tz/products) --}}
            <form id="shop-filter-form" class="cinematic-filter-bar">
                <div class="cinematic-search-input-wrap">
                    <i class="fa fa-search" aria-hidden="true"></i>
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="Search model, color, or powertrain...">
                </div>

                <select name="transmission" class="cinematic-filter-select" aria-label="Filter by transmission" data-selected="{{ request('transmission') }}">
                    <option value="">All Transmissions</option>
                </select>

                <select name="fuel_type" class="cinematic-filter-select" aria-label="Filter by fuel type" data-selected="{{ request('fuel_type') }}">
                    <option value="">All Powertrains</option>
                </select>

                <select name="sort" class="cinematic-filter-select" aria-label="Sort vehicles">
                    <option value="latest" @selected(request('sort', 'latest') === 'latest')>Newest Arrivals</option>
                    <option value="name" @selected(request('sort') === 'name')>Model Name A–Z</option>
                    <option value="price_low" @selected(request('sort') === 'price_low')>Investment: Low to High</option>
                    <option value="price_high" @selected(request('sort') === 'price_high')>Investment: High to Low</option>
                </select>

                <button type="submit" class="cinematic-btn cinematic-btn--gold" style="padding: 10px 18px;">
                    Filter
                </button>

                <button type="reset" class="cinematic-btn cinematic-btn--ghost" style="padding: 10px 14px; font-size: 11px;">
                    <i class="fa fa-times"></i> Reset
                </button>
            </form>

            {{-- VEHICLE CATALOG --}}
            <section id="product-grid" class="cinematic-catalog-grid" aria-label="Available Vehicles" aria-live="polite">
                <div class="cinematic-panel" style="grid-column: 1 / -1; text-align: center; padding: 40px 20px; color: var(--dim-white);">
                    Loading vehicles...
                </div>
            </section>
        </div>
    </main>

    {{-- PRODUCT DETAILS (GET /api/tz/products/{id}) --}}
    <dialog id="product-dialog" class="cinematic-panel" style="max-width: 560px; width: calc(100% - 32px); padding: 24px; color: #FFFFFF; background: #111; border: 1px solid rgba(255,255,255,0.12);">
        <div id="product-dialog-body"></div>
        <div style="display: flex; gap: 12px; margin-top: 20px;">
            @unless($isAdmin)
                <button type="button" id="product-dialog-add" class="cinematic-btn cinematic-btn--gold" style="flex: 1;">
                    <i class="fa fa-shopping-bag"></i> Add to Cart
                </button>
            @endunless
            <button type="button" id="product-dialog-close" class="cinematic-btn cinematic-btn--ghost">Close</button>
        </div>
    </dialog>

    @include('pages.partials.tz-footer')
    @include('pages.partials.tz-api')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const isAdmin = @json($isAdmin);
            const productsUrl = @json(route('api.tz.products.index'));
            const cartUrl = @json(route('api.tz.cart.store'));
            const editUrlTemplate = @json(route('furever.products.edit', '__ID__'));

            const form = document.getElementById('shop-filter-form');
            const grid = document.getElementById('product-grid');
            const flash = document.getElementById('shop-flash');
            const dialog = document.getElementById('product-dialog');
            const dialogBody = document.getElementById('product-dialog-body');
            const dialogAdd = document.getElementById('product-dialog-add');
            let dialogProductId = null;

            function fillSelect(select, values) {
                const current = select.value || select.dataset.selected || '';
                select.dataset.selected = '';
                select.length = 1;
                values.forEach(function (value) {
                    select.add(new Option(value, value, false, value === current));
                });
            }

            function productCard(product) {
                const image = window.tzImage(product.image);
                const inStock = product.stock > 0;
                const description = product.description
                    ? (product.description.length > 95 ? product.description.slice(0, 95) + '...' : product.description)
                    : '';

                const actions = isAdmin
                    ? '<a href="' + editUrlTemplate.replace('__ID__', product.id) + '" class="cinematic-btn cinematic-btn--ghost" title="Edit Vehicle"><i class="fa fa-pencil"></i></a>'
                    : '<button type="button" class="cinematic-btn cinematic-btn--gold" style="flex: 1;" data-add-to-cart="' + product.id + '"' + (inStock ? '' : ' disabled') + '>'
                        + '<i class="fa fa-shopping-bag"></i> <span>' + (inStock ? 'Add to Cart' : 'Out of stock') + '</span></button>';

                return '<article class="cinematic-vehicle-card">'
                    + '<div class="cinematic-vehicle-card__media">'
                    + '<div class="cinematic-vehicle-card__badges">'
                    + '<span class="cinematic-pill-tag cinematic-pill-tag--gold">' + window.tzEscape(product.year) + '</span>'
                    + (product.transmission ? '<span class="cinematic-pill-tag">' + window.tzEscape(product.transmission) + '</span>' : '')
                    + (product.fuel_type ? '<span class="cinematic-pill-tag" style="border-color: rgba(74, 144, 217, 0.3); color: var(--accent-blue-ice);">' + window.tzEscape(product.fuel_type) + '</span>' : '')
                    + '</div>'
                    + (image
                        ? '<img src="' + window.tzEscape(image) + '" alt="' + window.tzEscape(product.name) + '" loading="lazy">'
                        : '<div style="display:flex; height:100%; align-items:center; justify-content:center; color:rgba(255,255,255,0.15); font-size:54px;"><i class="fa fa-car"></i></div>')
                    + '</div>'
                    + '<div class="cinematic-vehicle-card__body">'
                    + '<h2 class="cinematic-vehicle-card__title">' + window.tzEscape(product.name) + '</h2>'
                    + '<div class="cinematic-vehicle-card__meta">'
                    + (product.color ? '<span><i class="fa fa-paint-brush" style="color:var(--accent-gold); margin-right:4px;"></i> ' + window.tzEscape(product.color) + '</span>' : '')
                    + '<span><i class="fa fa-check-circle" style="color: ' + (inStock ? '#4ade80' : '#f87171') + '; margin-right:4px;"></i> ' + (inStock ? product.stock + ' Available' : 'Out of stock') + '</span>'
                    + '</div>'
                    + (description ? '<p style="font-size: 13px; color: var(--dim-white); line-height: 1.55; margin-bottom: 16px; flex-grow: 1;">' + window.tzEscape(description) + '</p>' : '')
                    + '<div class="cinematic-vehicle-card__price">' + window.tzMoney(product.price) + '</div>'
                    + '<div class="cinematic-vehicle-card__actions">'
                    + '<button type="button" class="cinematic-btn cinematic-btn--ghost" data-view-product="' + product.id + '" title="View details"><i class="fa fa-info-circle"></i> Details</button>'
                    + actions
                    + '</div>'
                    + '</div>'
                    + '</article>';
            }

            async function loadProducts() {
                const params = new URLSearchParams(new FormData(form));
                Array.from(params.keys()).forEach(function (key) {
                    if (!params.get(key)) {
                        params.delete(key);
                    }
                });

                try {
                    const response = await window.tzApi(productsUrl + '?' + params.toString());
                    const products = response.data;

                    fillSelect(form.elements.transmission, response.filters.transmissions);
                    fillSelect(form.elements.fuel_type, response.filters.fuel_types);

                    document.getElementById('product-count').textContent = products.length;
                    document.getElementById('product-count-label').textContent = products.length === 1 ? 'Vehicle' : 'Vehicles';

                    grid.innerHTML = products.length
                        ? products.map(productCard).join('')
                        : '<div class="cinematic-panel" style="grid-column: 1 / -1; text-align: center; padding: 70px 20px;">'
                            + '<i class="fa fa-car" style="font-size: 48px; color: var(--whisper); margin-bottom: 16px;"></i>'
                            + '<h2 style="font-size: 20px; font-weight: 700; color: #FFFFFF; margin-bottom: 8px;">No vehicles match your criteria</h2>'
                            + '<p style="font-size: 14px; color: var(--dim-white);">Adjust your search or filters to view other vehicles.</p>'
                            + '</div>';
                } catch (error) {
                    window.tzFlash(flash, error.message, false);
                }
            }

            async function addToCart(productId) {
                try {
                    const response = await window.tzApi(cartUrl, {
                        method: 'POST',
                        body: { product_id: Number(productId), quantity: 1 },
                    });
                    window.tzSetCartCount(response.data.count);
                    window.tzFlash(flash, response.message, true);
                } catch (error) {
                    window.tzFlash(flash, error.message, false);
                }
                flash.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }

            async function showProduct(productId) {
                try {
                    const product = (await window.tzApi(productsUrl + '/' + productId)).data;
                    const image = window.tzImage(product.image);
                    dialogProductId = product.id;

                    dialogBody.innerHTML = (image ? '<img src="' + window.tzEscape(image) + '" alt="' + window.tzEscape(product.name) + '" style="width:100%; border-radius:8px; margin-bottom:16px;">' : '')
                        + '<h2 style="font-size: 22px; font-weight: 700; margin-bottom: 8px;">' + window.tzEscape(product.name) + '</h2>'
                        + '<p style="color: var(--accent-gold); font-size: 18px; font-weight: 700; margin-bottom: 12px;">' + window.tzMoney(product.price) + '</p>'
                        + '<ul style="list-style:none; padding:0; margin:0 0 12px; font-size:13px; color: var(--dim-white); line-height:1.8;">'
                        + '<li>Year: ' + window.tzEscape(product.year) + '</li>'
                        + '<li>Color: ' + window.tzEscape(product.color) + '</li>'
                        + '<li>Transmission: ' + window.tzEscape(product.transmission) + '</li>'
                        + '<li>Fuel: ' + window.tzEscape(product.fuel_type) + '</li>'
                        + '<li>Stock: ' + (product.stock > 0 ? product.stock + ' available' : 'Out of stock') + '</li>'
                        + '</ul>'
                        + '<p style="font-size: 14px; line-height: 1.6; color: var(--dim-white);">' + window.tzEscape(product.description) + '</p>';

                    if (dialogAdd) {
                        dialogAdd.disabled = product.stock <= 0;
                    }

                    dialog.showModal();
                } catch (error) {
                    window.tzFlash(flash, error.message, false);
                }
            }

            form.addEventListener('submit', function (event) {
                event.preventDefault();
                loadProducts();
            });

            form.addEventListener('reset', function () {
                setTimeout(function () {
                    form.elements.search.value = '';
                    form.elements.transmission.value = '';
                    form.elements.fuel_type.value = '';
                    form.elements.sort.value = 'latest';
                    loadProducts();
                });
            });

            grid.addEventListener('click', function (event) {
                const addButton = event.target.closest('[data-add-to-cart]');
                const viewButton = event.target.closest('[data-view-product]');

                if (addButton) {
                    addToCart(addButton.dataset.addToCart);
                } else if (viewButton) {
                    showProduct(viewButton.dataset.viewProduct);
                }
            });

            if (dialogAdd) {
                dialogAdd.addEventListener('click', function () {
                    dialog.close();
                    addToCart(dialogProductId);
                });
            }

            document.getElementById('product-dialog-close').addEventListener('click', function () {
                dialog.close();
            });

            loadProducts();
        });
    </script>
</x-layout>
