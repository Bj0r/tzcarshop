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
                <h1>
                    Your Toyota Cart
                </h1>
                <p>
                    Review the precision vehicles you have selected before finalizing your commission.
                </p>
            </div>



            @if(session('success'))

                <div class="tz-alert tz-alert--success" role="status">

                    <i class="fa fa-check-circle"></i>

                    {{ session('success') }}

                </div>

            @endif

            @if(session('error') || $errors->has('cart') || $errors->has('selected_items'))

                <div class="tz-alert tz-cart__alert--error" role="alert">

                    <i class="fa fa-exclamation-circle"></i>

                    {{
                        session('error')
                        ?: ($errors->first('cart') ?: $errors->first('selected_items'))
                    }}

                </div>

            @endif



            @if(count($cart) > 0)


                <div class="cart-layout">


                    {{-- CART ITEMS --}}
                    <div class="cart-items">


                        @foreach($cart as $id => $item)


                            <div class="cart-item">


                                {{-- CHECKOUT SELECTION --}}
                                <label class="cart-selection">

                                    <input
                                        type="checkbox"
                                        class="cart-item-selector"
                                        name="selected_items[]"
                                        value="{{ $id }}"
                                        form="checkout-form"
                                        data-unit-price="{{ number_format($item['price'], 2, '.', '') }}"
                                        data-quantity="{{ $item['quantity'] }}"
                                        checked
                                    >

                                    <span class="sr-only">

                                        Select {{ $item['name'] }} for checkout

                                    </span>

                                </label>


                                {{-- IMAGE --}}
                                <div class="cart-image">

                                    @if($item['image'])

                                        <img
                                            src="{{ filter_var($item['image'], FILTER_VALIDATE_URL) ? $item['image'] : asset('storage/'.$item['image']) }}"
                                            alt="{{ $item['name'] }}"
                                        >

                                    @else

                                        <i class="fa fa-car" aria-hidden="true"></i>

                                    @endif

                                </div>



                                {{-- DETAILS --}}
                                <div class="cart-product-info">

                                    <span>

                                        Toyota vehicle

                                    </span>

                                    <h3>

                                        {{ $item['name'] }}

                                    </h3>

                                    <strong>

                                        ₱{{
                                            number_format(
                                                $item['price'],
                                                2
                                            )
                                        }}

                                    </strong>

                                </div>



                                {{-- QUANTITY --}}
                                <div class="cart-quantity">

                                    <form
                                        action="{{
                                            route(
                                                'furever.update-cart',
                                                $id
                                            )
                                        }}"
                                        method="POST"
                                    >

                                        @csrf

                                        <label>

                                            Quantity

                                        </label>

                                        <input
                                            type="number"
                                            name="quantity"
                                            value="{{ $item['quantity'] }}"
                                            min="1"
                                            max="{{ $item['stock'] }}"
                                            onchange="this.form.submit()"
                                        >

                                    </form>

                                </div>



                                {{-- SUBTOTAL --}}
                                <div class="cart-subtotal">

                                    ₱{{
                                        number_format(
                                            $item['price']
                                            *
                                            $item['quantity'],
                                            2
                                        )
                                    }}

                                </div>



                                {{-- REMOVE --}}
                                <form
                                    action="{{ route('furever.remove-cart', $id) }}"
                                    method="POST"
                                    class="remove-cart-form"
                                    onsubmit="return confirm('Remove this item from your cart?')"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="remove-cart-btn" aria-label="Remove {{ $item['name'] }} from cart">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>


                            </div>


                        @endforeach


                    </div>



                    {{-- ORDER SUMMARY --}}
                    <div class="cart-summary">


                        <h3>

                            Order Summary

                        </h3>


                        <div class="summary-row">

                            <span>

                                Items

                            </span>

                            <span>

                                <span id="selected-item-count">

                                    {{ count($cart) }}

                                </span>

                            </span>

                        </div>


                        <div class="summary-row total">

                            <span>

                                Total

                            </span>

                            <strong id="selected-cart-total">

                                ₱{{
                                    number_format(
                                        $total,
                                        2
                                    )
                                }}

                            </strong>

                        </div>


                        <form
                            id="checkout-form"
                            action="{{ route('furever.checkout') }}"
                            method="POST"
                        >

                            @csrf

                            <button type="submit" class="checkout-btn">

                                Place Order

                                <i class="fa fa-arrow-right"></i>

                            </button>

                        </form>


                        <a
                            href="{{ route('furever.shop') }}"
                            class="continue-shopping"
                        >

                            <i class="fa fa-arrow-left"></i>

                            Continue Shopping

                        </a>


                    </div>


                </div>


            @else


                {{-- EMPTY CART --}}
                <div class="empty-cart tz-cart__empty">

                    <div>

                        <i class="fa fa-car" aria-hidden="true"></i>

                    </div>


                    <h2>

                        Your garage is waiting

                    </h2>


                    <p>

                        Explore the showroom and choose a Toyota that fits your drive.

                    </p>


                    <a
                        href="{{ route('furever.shop') }}"
                            class="tz-button tz-button--primary"
                    >

                        Browse vehicles

                    </a>

                </div>


            @endif


        </main>


    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const itemSelectors = document.querySelectorAll('.cart-item-selector');
            const selectedItemCount = document.getElementById('selected-item-count');
            const selectedCartTotal = document.getElementById('selected-cart-total');

            if (!selectedItemCount || !selectedCartTotal) {
                return;
            }

            const updateCheckoutSummary = function () {
                const selectedItems = Array.from(itemSelectors)
                    .filter(function (selector) {
                        return selector.checked;
                    });
                const total = selectedItems.reduce(function (sum, selector) {
                    return sum
                        + (Number(selector.dataset.unitPrice)
                            * Number(selector.dataset.quantity));
                }, 0);

                selectedItemCount.textContent = selectedItems.length;
                selectedCartTotal.textContent = '₱' + total.toLocaleString(
                    'en-PH',
                    {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2,
                    }
                );
            };

            itemSelectors.forEach(function (selector) {
                selector.addEventListener('change', updateCheckoutSummary);
            });
        });
    </script>

    @include('pages.partials.tz-footer')
</x-layout>
