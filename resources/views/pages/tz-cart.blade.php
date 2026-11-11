<x-layout>

    @push('styles')
        <link
            rel="stylesheet"
            href="{{ asset('css/tz.css') }}"
        >
    @endpush

    <div class="tz-page tz-cart-page" data-tz-cart>

        @include('pages.partials.tz-nav')

        <main class="cart-page tz-cart__main">

            <div class="cart-heading tz-cart__heading">
                <span class="section-label">
                    TZ ATELIER &bull; TZ CARSHOP
                </span>
                <h1>Your Toyota Cart</h1>
                <p>Review the precision vehicles you have selected before finalizing your commission.</p>
            </div>

            <div id="cart-success" class="tz-alert tz-alert--success" role="status" hidden></div>
            <div id="cart-error" class="tz-alert tz-cart__alert--error" role="alert" hidden></div>

            {{-- CART WITH ITEMS (data from GET /api/tz/cart) --}}
            <div id="cart-layout" class="cart-layout" hidden>

                <div id="cart-items" class="cart-items"></div>

                <div class="cart-summary">
                    <h3>Order Summary</h3>

                    <div class="summary-row">
                        <span>Items</span>
                        <span id="selected-item-count">0</span>
                    </div>

                    <div class="summary-row total">
                        <span>Total</span>
                        <strong id="selected-cart-total">₱0.00</strong>
                    </div>

                    <button type="button" id="checkout-button" class="checkout-btn">
                        Place Order
                        <i class="fa fa-arrow-right"></i>
                    </button>

                    <a href="{{ route('furever.shop') }}" class="continue-shopping">
                        <i class="fa fa-arrow-left"></i>
                        Continue Shopping
                    </a>
                </div>
            </div>

            {{-- EMPTY CART --}}
            <div id="cart-empty" class="empty-cart tz-cart__empty" hidden>
                <div>
                    <i class="fa fa-car" aria-hidden="true"></i>
                </div>
                <h2>Your garage is waiting</h2>
                <p>Explore the showroom and choose a Toyota that fits your drive.</p>
                <a href="{{ route('furever.shop') }}" class="tz-button tz-button--primary">
                    Browse vehicles
                </a>
            </div>

        </main>

    </div>

    @include('pages.partials.tz-footer')
    @include('pages.partials.tz-api')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const cartUrl = @json(route('api.tz.cart.index'));
            const ordersUrl = @json(route('api.tz.orders.store'));
            const ordersPageUrl = @json(route('furever.customer-orders'));

            const layout = document.getElementById('cart-layout');
            const itemsBox = document.getElementById('cart-items');
            const emptyBox = document.getElementById('cart-empty');
            const successBox = document.getElementById('cart-success');
            const errorBox = document.getElementById('cart-error');
            const selectedCount = document.getElementById('selected-item-count');
            const selectedTotal = document.getElementById('selected-cart-total');
            const checkoutButton = document.getElementById('checkout-button');
            let items = [];

            function showMessage(message, isSuccess) {
                successBox.hidden = !isSuccess;
                errorBox.hidden = isSuccess;
                (isSuccess ? successBox : errorBox).innerHTML = '<i class="fa ' + (isSuccess ? 'fa-check-circle' : 'fa-exclamation-circle') + '"></i> ' + window.tzEscape(message);
            }

            function selectedIds() {
                return Array.from(itemsBox.querySelectorAll('.cart-item-selector:checked'))
                    .map(function (checkbox) { return Number(checkbox.value); });
            }

            function updateSummary() {
                const ids = selectedIds();
                const total = items
                    .filter(function (item) { return ids.includes(item.product_id); })
                    .reduce(function (sum, item) { return sum + item.subtotal; }, 0);

                selectedCount.textContent = ids.length;
                selectedTotal.textContent = window.tzMoney(total);
                checkoutButton.disabled = ids.length === 0;
            }

            function render(cart) {
                const previouslyUnchecked = Array.from(itemsBox.querySelectorAll('.cart-item-selector:not(:checked)'))
                    .map(function (checkbox) { return Number(checkbox.value); });

                items = cart.items;
                window.tzSetCartCount(cart.count);

                layout.hidden = items.length === 0;
                emptyBox.hidden = items.length > 0;

                itemsBox.innerHTML = items.map(function (item) {
                    const image = window.tzImage(item.image);

                    return '<div class="cart-item">'
                        + '<label class="cart-selection">'
                        + '<input type="checkbox" class="cart-item-selector" value="' + item.product_id + '"' + (previouslyUnchecked.includes(item.product_id) ? '' : ' checked') + '>'
                        + '<span class="sr-only">Select ' + window.tzEscape(item.name) + ' for checkout</span>'
                        + '</label>'
                        + '<div class="cart-image">'
                        + (image ? '<img src="' + window.tzEscape(image) + '" alt="' + window.tzEscape(item.name) + '">' : '<i class="fa fa-car" aria-hidden="true"></i>')
                        + '</div>'
                        + '<div class="cart-product-info">'
                        + '<span>Toyota vehicle</span>'
                        + '<h3>' + window.tzEscape(item.name) + '</h3>'
                        + '<strong>' + window.tzMoney(item.price) + '</strong>'
                        + '</div>'
                        + '<div class="cart-quantity">'
                        + '<label>Quantity</label>'
                        + '<input type="number" min="1" max="' + item.stock + '" value="' + item.quantity + '" data-quantity-for="' + item.product_id + '">'
                        + '</div>'
                        + '<div class="cart-subtotal">' + window.tzMoney(item.subtotal) + '</div>'
                        + '<button type="button" class="remove-cart-btn" data-remove="' + item.product_id + '" aria-label="Remove ' + window.tzEscape(item.name) + ' from cart">'
                        + '<i class="fa fa-trash"></i>'
                        + '</button>'
                        + '</div>';
                }).join('');

                updateSummary();
            }

            async function loadCart() {
                try {
                    render((await window.tzApi(cartUrl)).data);
                } catch (error) {
                    showMessage(error.message, false);
                }
            }

            itemsBox.addEventListener('change', async function (event) {
                if (event.target.matches('.cart-item-selector')) {
                    updateSummary();
                    return;
                }

                const quantityInput = event.target.closest('[data-quantity-for]');

                if (!quantityInput) {
                    return;
                }

                try {
                    const response = await window.tzApi(cartUrl + '/' + quantityInput.dataset.quantityFor, {
                        method: 'PATCH',
                        body: { quantity: Number(quantityInput.value) },
                    });
                    render(response.data);
                    showMessage(response.message, true);
                } catch (error) {
                    showMessage(error.message, false);
                    loadCart();
                }
            });

            itemsBox.addEventListener('click', async function (event) {
                const removeButton = event.target.closest('[data-remove]');

                if (!removeButton || !confirm('Remove this item from your cart?')) {
                    return;
                }

                try {
                    const response = await window.tzApi(cartUrl + '/' + removeButton.dataset.remove, { method: 'DELETE' });
                    render(response.data);
                    showMessage(response.message, true);
                } catch (error) {
                    showMessage(error.message, false);
                }
            });

            checkoutButton.addEventListener('click', async function () {
                checkoutButton.disabled = true;

                try {
                    const response = await window.tzApi(ordersUrl, {
                        method: 'POST',
                        body: { selected_items: selectedIds() },
                    });
                    window.location.href = ordersPageUrl + '?placed=' + response.data.id;
                } catch (error) {
                    showMessage(error.message, false);
                    loadCart();
                }
            });

            loadCart();
        });
    </script>
</x-layout>
