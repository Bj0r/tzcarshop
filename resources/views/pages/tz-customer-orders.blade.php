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
                        Commissions Placed: <strong style="color: var(--accent-gold); margin-left: 6px;">{{ $orders->count() }}</strong>
                    </span>
                </div>
            </header>

            @if(session('success'))
                <div class="cinematic-panel" style="padding: 16px 20px; margin-bottom: 24px; border-color: rgba(45, 138, 94, 0.4); background: rgba(45, 138, 94, 0.1);">
                    <span style="color: #4ade80; font-size: 13px; font-weight: 600;">
                        <i class="fa fa-check-circle"></i> {{ session('success') }}
                    </span>
                </div>
            @endif

            @if(session('error'))
                <div class="cinematic-panel" style="padding: 16px 20px; margin-bottom: 24px; border-color: rgba(196, 77, 90, 0.4); background: rgba(196, 77, 90, 0.1);">
                    <span style="color: #f87171; font-size: 13px; font-weight: 600;">
                        <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
                    </span>
                </div>
            @endif

            <section class="cinematic-panel">
                @if($orders->isNotEmpty())
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
                            <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td><strong style="color: var(--accent-gold);">#{{ $order->id }}</strong></td>
                                        <td>
                                            <ul style="list-style: none; padding: 0; margin: 0;">
                                                @foreach($order->items as $item)
                                                    <li style="font-weight: 600; color: #FFFFFF;">
                                                        <span style="color: var(--accent-gold); font-size: 12px; margin-right: 4px;">{{ $item->quantity }}&times;</span>
                                                        {{ $item->product_name }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </td>
                                        <td><strong style="color: #FFFFFF; font-size: 14px;">&#8369;{{ number_format($order->total, 2) }}</strong></td>
                                        <td style="color: var(--dim-white);">{{ $order->created_at->format('M d, Y') }}</td>
                                        <td style="color: var(--dim-white);">{{ $order->received_at?->format('M d, Y') ?? '—' }}</td>
                                        <td>
                                            <span class="cinematic-status-pill cinematic-status--{{ $order->status }} fur-order-status fur-order-status--{{ $order->status }}">
                                                {{ $order->status_label }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($order->canBeMarkedReceivedByCustomer())
                                                <form action="{{ route('furever.customer-orders.received', $order) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="cinematic-btn cinematic-btn--gold fur-button fur-order-received-button" style="padding: 6px 14px; font-size: 11px;">
                                                        <i class="fa fa-check"></i> Order Received
                                                    </button>
                                                </form>
                                            @elseif($order->canBeCancelledByCustomer())
                                                <form action="{{ route('furever.customer-orders.cancel', $order) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="cinematic-btn cinematic-btn--danger fur-button fur-order-cancel-button" style="padding: 6px 14px; font-size: 11px;">
                                                        <i class="fa fa-times"></i> Cancel Order
                                                    </button>
                                                </form>
                                            @else
                                                <span style="font-size: 11px; color: var(--whisper); text-transform: uppercase; letter-spacing: 0.08em;" class="fur-order-action-note">
                                                    {{ $order->status === \App\Models\Order::STATUS_PROCESSING ? 'In Preparation' : ($order->status === \App\Models\Order::STATUS_COMPLETED ? 'Delivered' : 'Cancelled') }}
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div style="text-align: center; padding: 60px 20px;">
                        <i class="fa fa-car" style="font-size: 48px; color: var(--whisper); margin-bottom: 16px;"></i>
                        <h2 style="font-size: 20px; font-weight: 700; color: #FFFFFF; margin-bottom: 8px;">No Commissions Placed Yet</h2>
                        <p style="font-size: 14px; color: var(--dim-white); margin-bottom: 24px;">When you reserve or acquire a vehicle, its real-time status will appear here.</p>
                        <a href="{{ route('furever.shop') }}" class="cinematic-btn cinematic-btn--gold">Browse Collection</a>
                    </div>
                @endif
            </section>
        </div>
    </main>

    @include('pages.partials.tz-footer')
</x-layout>
