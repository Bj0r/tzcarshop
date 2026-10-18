<x-layout>
    <x-slot:title>TZ ATELIER — Cart &amp; Order Management</x-slot:title>

    @include('pages.partials.tz-admin-sidebar')

    <main class="cinematic-subpage-wrap fur-storefront">
        <div class="cinematic-subpage-container fur-shell fur-admin-page">
            <header class="cinematic-subpage-header fur-admin-heading">
                <div>
                    <span class="cinematic-subpage-eyebrow fur-eyebrow fur-eyebrow--orange">Order management</span>
                    <h1 class="cinematic-subpage-title">Review and manage customer orders</h1>
                    <p class="cinematic-subpage-subtitle">Track each vehicle commission placed by a customer and update its fulfilment status.</p>
                </div>
            </header>

            @if(session('success'))
                <div class="cinematic-panel fur-message fur-message--success" style="padding: 16px 20px; margin-bottom: 24px; border-color: rgba(45, 138, 94, 0.4); background: rgba(45, 138, 94, 0.1);">
                    <span style="color: #4ade80; font-size: 13px; font-weight: 600;">
                        <i class="fa fa-check-circle"></i> {{ session('success') }}
                    </span>
                </div>
            @endif

            @if(session('error'))
                <div class="cinematic-panel fur-message fur-message--error" style="padding: 16px 20px; margin-bottom: 24px; border-color: rgba(196, 77, 90, 0.4); background: rgba(196, 77, 90, 0.1);">
                    <span style="color: #f87171; font-size: 13px; font-weight: 600;">
                        <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
                    </span>
                </div>
            @endif

            {{-- ORDERS STAT DECK --}}
            <section class="cinematic-stat-deck fur-admin-stat-grid" aria-label="Order overview">
                <article class="cinematic-stat-card fur-card">
                    <span class="cinematic-stat-label">Total orders</span>
                    <strong class="cinematic-stat-value">{{ $totalOrders }}</strong>
                </article>
                <article class="cinematic-stat-card fur-card">
                    <span class="cinematic-stat-label">Pending orders</span>
                    <strong class="cinematic-stat-value" style="color: var(--accent-gold);">{{ $pendingOrders }}</strong>
                </article>
                <article class="cinematic-stat-card fur-card">
                    <span class="cinematic-stat-label">Completed orders</span>
                    <strong class="cinematic-stat-value" style="color: #4ade80;">{{ $completedOrders }}</strong>
                </article>
            </section>

            {{-- ORDERS DIRECTORY --}}
            <section class="cinematic-panel fur-card fur-customer-table-card">
                <div class="cinematic-panel-header fur-customer-table-card__header">
                    <div>
                        <h2 class="cinematic-panel-title">Customer orders</h2>
                        <p style="font-size: 13px; color: var(--dim-white); margin-top: 4px;">Search by order number, customer name, username, or email.</p>
                    </div>

                    <form action="{{ route('furever.orders') }}" method="GET" class="cinematic-filter-bar fur-admin-search" style="padding: 0; border: none; background: none; margin-bottom: 0;">
                        <div class="cinematic-search-input-wrap">
                            <i class="fa fa-search" aria-hidden="true"></i>
                            <input type="search" name="search" value="{{ request('search') }}" placeholder="Search orders..." aria-label="Search orders">
                        </div>
                        <button type="submit" class="cinematic-btn cinematic-btn--gold" style="padding: 10px 18px;">Search</button>
                    </form>
                </div>

                <div class="cinematic-table-wrap fur-customer-table-wrap">
                    <table class="cinematic-table fur-customer-table fur-orders-table">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Customer</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Placed</th>
                                <th>Status &amp; Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                <tr>
                                    <td><strong style="color: var(--accent-gold);">#{{ $order->id }}</strong></td>
                                    <td>
                                        <strong style="color: #FFFFFF; display: block;">{{ $order->customer->name }}</strong>
                                        <span class="fur-order-customer-detail" style="color: var(--dim-white); font-size: 12px;">{{ $order->customer->email }}</span>
                                    </td>
                                    <td>
                                        <ul class="fur-order-items" style="list-style: none; padding: 0; margin: 0;">
                                            @foreach($order->items as $item)
                                                <li style="color: #FFFFFF; font-size: 13px;">
                                                    <span style="color: var(--accent-gold); font-weight: 700;">{{ $item->quantity }}&times;</span>
                                                    {{ $item->product_name }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td><strong style="color: #FFFFFF; font-size: 14px;">&#8369;{{ number_format($order->total, 2) }}</strong></td>
                                    <td style="color: var(--dim-white);">{{ $order->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 8px;">
                                            <span class="cinematic-status-pill cinematic-status--{{ $order->status }} fur-order-status fur-order-status--{{ $order->status }}">
                                                {{ $order->status_label }}
                                            </span>

                                            @if($order->availableAdminTransitions())
                                                <form action="{{ route('furever.orders.status', $order) }}" method="POST" class="fur-status-form fur-order-transition-form" style="display: inline-flex; align-items: center; gap: 6px;">
                                                    @csrf
                                                    @method('PATCH')
                                                    <select name="status" aria-label="Choose the next action for order #{{ $order->id }}" class="cinematic-filter-select" style="padding: 4px 10px; font-size: 11px;">
                                                        @foreach($order->availableAdminTransitions() as $status => $label)
                                                            <option value="{{ $status }}">{{ $label }}</option>
                                                        @endforeach
                                                    </select>
                                                    <button type="submit" class="cinematic-btn cinematic-btn--gold fur-button fur-order-transition-button" style="padding: 5px 12px; font-size: 11px;">Update</button>
                                                </form>
                                            @elseif($order->status === \App\Models\Order::STATUS_READY_FOR_COLLECTION)
                                                <span class="fur-order-action-note" style="font-size: 11px; color: var(--dim-white);">Awaiting customer confirmation</span>
                                            @else
                                                <span class="fur-order-action-note" style="font-size: 11px; color: var(--whisper);">No further action</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="fur-customer-table__empty" style="text-align: center; padding: 40px 0; color: var(--dim-white);">No customer orders match your search.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </main>
</x-layout>
