<x-layout>
    <x-slot:title>TZ ATELIER — Vehicle Commissions &amp; Orders</x-slot:title>

    @include('pages.partials.tz-nav')

    <main class="cinematic-subpage-wrap">
        <div class="cinematic-subpage-container">
            <header class="cinematic-subpage-header">
                <div>
                    <span class="cinematic-subpage-eyebrow">TZ ATELIER &bull; CLIENT PORTFOLIO</span>
                    <h1 class="cinematic-subpage-title">Your orders &bull; Vehicle Commissions</h1>
                    <p class="cinematic-subpage-subtitle">Track the allocation, preparation, and handover status of your commissioned vehicles.</p>
                </div>
                <div>
                    <span class="nav-badge-link" style="font-size: 12px;">
                        Commissions Placed: <strong id="order-count" style="color: var(--accent-gold); margin-left: 6px;">0</strong>
                    </span>
                </div>
            </header>

            <div id="orders-flash" class="cinematic-panel" style="padding: 16px 20px; margin-bottom: 24px;" role="status" hidden></div>

            {{-- ORDER HISTORY (data from GET /api/tz/orders) --}}
            <section class="cinematic-panel">
                <div id="orders-list" hidden>
                    <div class="cinematic-panel-header">
                        <div>
                            <h2 class="cinematic-panel-title">Commission Records</h2>
                            <p style="font-size: 13px; color: var(--dim-white); margin-top: 4px;">Orders transition through Pending, Processing, to Ready for collection. Confirm receipt upon vehicle handover.</p>
                        </div>
                    </div>

                    <div class="cinematic-table-wrap">
                        <table class="cinematic-table fur-customer-table fur-customer-orders-table">
                            <thead>
                                <tr>
                                    <th>Commission #</th>
                                    <th>Vehicle Allocation</th>
                                    <th>Total Investment</th>
                                    <th>Placed</th>
                                    <th>Handover Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="orders-body"></tbody>
                        </table>
                    </div>
                </div>

                <div id="orders-empty" style="text-align: center; padding: 60px 20px;" hidden>
                    <i class="fa fa-car" style="font-size: 48px; color: var(--whisper); margin-bottom: 16px;"></i>
                    <h2 style="font-size: 20px; font-weight: 700; color: #FFFFFF; margin-bottom: 8px;">No Commissions Placed Yet</h2>
                    <p style="font-size: 14px; color: var(--dim-white); margin-bottom: 24px;">When you reserve or acquire a vehicle, its real-time status will appear here.</p>
                    <a href="{{ route('furever.shop') }}" class="cinematic-btn cinematic-btn--gold">Browse Collection</a>
                </div>
            </section>
        </div>
    </main>

    @include('pages.partials.tz-footer')
    @include('pages.partials.tz-api')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ordersUrl = @json(route('api.tz.orders.index'));

            const list = document.getElementById('orders-list');
            const empty = document.getElementById('orders-empty');
            const body = document.getElementById('orders-body');
            const flash = document.getElementById('orders-flash');

            function formatDate(value) {
                return value
                    ? new Date(value).toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' })
                    : '—';
            }

            function actionCell(order) {
                if (order.can_mark_received) {
                    return '<button type="button" class="cinematic-btn cinematic-btn--gold fur-button fur-order-received-button" style="padding: 6px 14px; font-size: 11px;" data-order-action="received" data-order-id="' + order.id + '">'
                        + '<i class="fa fa-check"></i> Order Received</button>';
                }

                if (order.can_cancel) {
                    return '<button type="button" class="cinematic-btn cinematic-btn--danger fur-button fur-order-cancel-button" style="padding: 6px 14px; font-size: 11px;" data-order-action="cancel" data-order-id="' + order.id + '">'
                        + '<i class="fa fa-times"></i> Cancel Order</button>';
                }

                const note = order.status === 'processing' ? 'In Preparation' : (order.status === 'completed' ? 'Delivered' : 'Cancelled');

                return '<span style="font-size: 11px; color: var(--whisper); text-transform: uppercase; letter-spacing: 0.08em;" class="fur-order-action-note">' + note + '</span>';
            }

            function render(orders) {
                document.getElementById('order-count').textContent = orders.length;
                list.hidden = orders.length === 0;
                empty.hidden = orders.length > 0;

                body.innerHTML = orders.map(function (order) {
                    const items = order.items.map(function (item) {
                        return '<li style="font-weight: 600; color: #FFFFFF;">'
                            + '<span style="color: var(--accent-gold); font-size: 12px; margin-right: 4px;">' + item.quantity + '&times;</span>'
                            + window.tzEscape(item.product_name) + '</li>';
                    }).join('');

                    return '<tr>'
                        + '<td><strong style="color: var(--accent-gold);">#' + order.id + '</strong></td>'
                        + '<td><ul style="list-style: none; padding: 0; margin: 0;">' + items + '</ul></td>'
                        + '<td><strong style="color: #FFFFFF; font-size: 14px;">' + window.tzMoney(order.total) + '</strong></td>'
                        + '<td style="color: var(--dim-white);">' + formatDate(order.created_at) + '</td>'
                        + '<td style="color: var(--dim-white);">' + formatDate(order.received_at) + '</td>'
                        + '<td><span class="cinematic-status-pill cinematic-status--' + window.tzEscape(order.status) + ' fur-order-status fur-order-status--' + window.tzEscape(order.status) + '">' + window.tzEscape(order.status_label) + '</span></td>'
                        + '<td>' + actionCell(order) + '</td>'
                        + '</tr>';
                }).join('');
            }

            async function loadOrders() {
                try {
                    render((await window.tzApi(ordersUrl)).data);
                } catch (error) {
                    window.tzFlash(flash, error.message, false);
                }
            }

            body.addEventListener('click', async function (event) {
                const button = event.target.closest('[data-order-action]');

                if (!button) {
                    return;
                }

                if (button.dataset.orderAction === 'cancel' && !confirm('Cancel this order?')) {
                    return;
                }

                try {
                    const response = await window.tzApi(ordersUrl + '/' + button.dataset.orderId + '/' + button.dataset.orderAction, { method: 'PATCH' });
                    window.tzFlash(flash, response.message, true);
                } catch (error) {
                    window.tzFlash(flash, error.message, false);
                }

                loadOrders();
            });

            const placedOrderId = new URLSearchParams(window.location.search).get('placed');

            if (placedOrderId) {
                window.tzFlash(flash, 'Order #' + placedOrderId + ' has been placed successfully. You can track its status here.', true);
            }

            loadOrders();
        });
    </script>
</x-layout>
