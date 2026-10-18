<x-layout>
    <x-slot:title>TZ ATELIER — Admin Dashboard</x-slot:title>
    @push('styles')<link rel="stylesheet" href="{{ asset('css/tz.css') }}">@endpush

    @include('pages.partials.tz-admin-sidebar')

    <main class="cinematic-subpage-wrap tz-page">
        <div class="cinematic-subpage-container tz-inventory">
            <header class="cinematic-subpage-header tz-hero">
                <div>
                    <span class="cinematic-subpage-eyebrow tz-eyebrow">TZ ATELIER &bull; EXECUTIVE DESK</span>
                    <h1 class="cinematic-subpage-title">Admin dashboard</h1>
                    <p class="cinematic-subpage-subtitle">Manage your Toyota inventory, customers, carts, and orders.</p>
                </div>
                <div>
                    <a href="{{ route('furever.products.create') }}" class="cinematic-btn cinematic-btn--gold tz-button tz-button--primary">
                        <i class="fa fa-plus" aria-hidden="true"></i> Add New Car
                    </a>
                </div>
            </header>

            {{-- STAT DECK --}}
            <section class="cinematic-stat-deck tz-dashboard-grid" aria-label="Shop overview">
                <article class="cinematic-stat-card">
                    <span class="cinematic-stat-label"><i class="fa fa-car" style="color:var(--accent-gold); margin-right:6px;"></i> Total vehicles</span>
                    <strong class="cinematic-stat-value">{{ $totalProducts }}</strong>
                </article>
                <article class="cinematic-stat-card">
                    <span class="cinematic-stat-label"><i class="fa fa-cubes" style="color:var(--accent-blue-ice); margin-right:6px;"></i> Total stock</span>
                    <strong class="cinematic-stat-value">{{ $totalStock }}</strong>
                </article>
                <article class="cinematic-stat-card">
                    <span class="cinematic-stat-label"><i class="fa fa-cogs" style="color:var(--accent-gold); margin-right:6px;"></i> Transmissions</span>
                    <strong class="cinematic-stat-value">{{ $totalCategories }}</strong>
                </article>
                <article class="cinematic-stat-card">
                    <span class="cinematic-stat-label"><i class="fa fa-users" style="color:var(--accent-blue-ice); margin-right:6px;"></i> Total customers</span>
                    <strong class="cinematic-stat-value">{{ $totalCustomers }}</strong>
                </article>
                <article class="cinematic-stat-card">
                    <span class="cinematic-stat-label"><i class="fa fa-user-check" style="color:#4ade80; margin-right:6px;"></i> Active customers</span>
                    <strong class="cinematic-stat-value">{{ $activeCustomers }}</strong>
                </article>
            </section>

            {{-- LOW STOCK WATCH PANEL --}}
            <section class="cinematic-panel tz-dashboard-panel">
                <div class="cinematic-panel-header">
                    <div>
                        <span class="cinematic-subpage-eyebrow tz-eyebrow">INVENTORY SURVEILLANCE</span>
                        <h2 class="cinematic-panel-title">Vehicles with limited stock</h2>
                    </div>
                    <a href="{{ route('furever.products') }}" class="cinematic-btn cinematic-btn--ghost tz-button tz-button--outline" style="font-size: 11px;">
                        Open inventory
                    </a>
                </div>

                <div class="tz-dashboard-list" style="display: flex; flex-direction: column; gap: 12px;">
                    @forelse($lowStockProducts as $product)
                        <article class="cinematic-panel" style="padding: 16px 20px; margin-bottom: 0; display: flex; align-items: center; justify-content: space-between; gap: 16px; background: rgba(255,255,255,0.02);">
                            <div style="display: flex; align-items: center; gap: 16px;">
                                <span style="width: 40px; height: 40px; display: grid; place-items: center; background: rgba(201,169,110,0.1); border: 1px solid rgba(201,169,110,0.2); color: var(--accent-gold);">
                                    <i class="fa fa-car" aria-hidden="true"></i>
                                </span>
                                <div>
                                    <strong style="color: #FFFFFF; font-size: 15px; display: block;">{{ $product->name }}</strong>
                                    <small style="color: var(--dim-white); font-size: 12px;">{{ $product->year }} &bull; {{ $product->transmission }}</small>
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 16px;">
                                <span class="cinematic-pill-tag cinematic-pill-tag--gold" style="font-size: 11px;">
                                    <b>{{ $product->stock }} in stock</b>
                                </span>
                                <a href="{{ route('furever.products.edit', $product) }}" class="cinematic-btn cinematic-btn--ghost" style="padding: 6px 14px; font-size: 11px;">
                                    Edit
                                </a>
                            </div>
                        </article>
                    @empty
                        <p class="tz-dashboard-empty" style="color: var(--dim-white); text-align: center; padding: 40px 0;">No low-stock vehicles right now.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </main>
</x-layout>
