<x-layout>
    <x-slot:title>TZ ATELIER — The Vehicle Collection &amp; Inventory</x-slot:title>

    @include('pages.partials.tz-nav')

    <main class="cinematic-subpage-wrap">
        <div class="cinematic-subpage-container">
            @if(session('success') || session('error'))
                <div class="cinematic-panel" style="padding: 16px 20px; margin-bottom: 24px; border-color: {{ session('success') ? 'rgba(45, 138, 94, 0.4)' : 'rgba(196, 77, 90, 0.4)' }}; background: {{ session('success') ? 'rgba(45, 138, 94, 0.1)' : 'rgba(196, 77, 90, 0.1)' }};">
                    <span style="color: {{ session('success') ? '#4ade80' : '#f87171' }}; font-size: 13px; font-weight: 600;">
                        <i class="fa {{ session('success') ? 'fa-check-circle' : 'fa-exclamation-circle' }}"></i> {{ session('success') ?? session('error') }}
                    </span>
                </div>
            @endif

            <header class="cinematic-subpage-header">
                <div>
                    <span class="cinematic-subpage-eyebrow">TZ ATELIER &bull; Toyota Curation &bull; TZ Carshop</span>
                    <h1 class="cinematic-subpage-title">Choose Your Toyota</h1>
                    <p class="cinematic-subpage-subtitle">Handcrafted automotive engineering. Precision grand tourers, hybrid executive sedans, and performance chassis available for acquisition.</p>
                </div>
                <div style="display: flex; gap: 12px; align-items: center;">
                    <span class="nav-badge-link" style="font-size: 12px;">
                        <strong style="color: var(--accent-gold);">{{ $products->count() }}</strong> {{ \Illuminate\Support\Str::plural('Vehicle', $products->count()) }}
                    </span>
                    @auth
                        @if(auth()->user()->isFureverAdmin())
                            <a href="{{ route('furever.products.create') }}" class="cinematic-btn cinematic-btn--gold" style="font-size: 11px;">
                                <i class="fa fa-plus"></i> Add Car
                            </a>
                        @endif
                    @endauth
                </div>
            </header>

            {{-- CINEMATIC FILTER STRIP --}}
            <form action="{{ route('furever.shop') }}" method="GET" class="cinematic-filter-bar">
                <div class="cinematic-search-input-wrap">
                    <i class="fa fa-search" aria-hidden="true"></i>
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="Search model, color, or powertrain...">
                </div>

                @if(!empty($transmissions) && count($transmissions) > 0)
                    <select name="transmission" class="cinematic-filter-select" aria-label="Filter by transmission">
                        <option value="">All Transmissions</option>
                        @foreach($transmissions as $trans)
                            <option value="{{ $trans }}" @selected(request('transmission') === $trans)>{{ $trans }}</option>
                        @endforeach
                    </select>
                @endif

                @if(!empty($fuelTypes) && count($fuelTypes) > 0)
                    <select name="fuel_type" class="cinematic-filter-select" aria-label="Filter by fuel type">
                        <option value="">All Powertrains</option>
                        @foreach($fuelTypes as $fuel)
                            <option value="{{ $fuel }}" @selected(request('fuel_type') === $fuel)>{{ $fuel }}</option>
                        @endforeach
                    </select>
                @endif

                <select name="sort" class="cinematic-filter-select" aria-label="Sort vehicles">
                    <option value="latest" @selected(request('sort', 'latest') === 'latest')>Newest Arrivals</option>
                    <option value="name" @selected(request('sort') === 'name')>Model Name A–Z</option>
                    <option value="price_low" @selected(request('sort') === 'price_low')>Investment: Low to High</option>
                    <option value="price_high" @selected(request('sort') === 'price_high')>Investment: High to Low</option>
                </select>

                <button type="submit" class="cinematic-btn cinematic-btn--gold" style="padding: 10px 18px;">
                    Filter
                </button>

                @if(request()->filled('search') || request()->filled('transmission') || request()->filled('fuel_type') || request('sort', 'latest') !== 'latest')
                    <a href="{{ route('furever.shop') }}" class="cinematic-btn cinematic-btn--ghost" style="padding: 10px 14px; font-size: 11px;">
                        <i class="fa fa-times"></i> Reset
                    </a>
                @endif
            </form>

            {{-- VEHICLE COLLECTION CATALOG --}}
            <section class="cinematic-catalog-grid" aria-label="Available Vehicles">
                @forelse($products as $product)
                    @php
                        $carImage = $product->image 
                            ? (filter_var($product->image, FILTER_VALIDATE_URL) ? $product->image : asset('storage/' . $product->image))
                            : null;
                    @endphp
                    <article class="cinematic-vehicle-card">
                        <div class="cinematic-vehicle-card__media">
                            <div class="cinematic-vehicle-card__badges">
                                <span class="cinematic-pill-tag cinematic-pill-tag--gold">{{ $product->year }}</span>
                                @if($product->transmission)
                                    <span class="cinematic-pill-tag">{{ $product->transmission }}</span>
                                @endif
                                @if($product->fuel_type)
                                    <span class="cinematic-pill-tag" style="border-color: rgba(74, 144, 217, 0.3); color: var(--accent-blue-ice);">{{ $product->fuel_type }}</span>
                                @endif
                            </div>

                            @if($carImage)
                                <img src="{{ $carImage }}" alt="{{ $product->name }}" loading="lazy">
                            @else
                                <div style="display:flex; height:100%; align-items:center; justify-content:center; color:rgba(255,255,255,0.15); font-size:54px;">
                                    <i class="fa fa-car"></i>
                                </div>
                            @endif
                        </div>

                        <div class="cinematic-vehicle-card__body">
                            <h2 class="cinematic-vehicle-card__title">{{ $product->name }}</h2>
                            <div class="cinematic-vehicle-card__meta">
                                @if($product->color)
                                    <span><i class="fa fa-paint-brush" style="color:var(--accent-gold); margin-right:4px;"></i> {{ $product->color }}</span>
                                @endif
                                <span><i class="fa fa-check-circle" style="color: {{ $product->stock > 0 ? '#4ade80' : '#f87171' }}; margin-right:4px;"></i> {{ $product->stock > 0 ? $product->stock . ' Available' : 'Allocated' }}</span>
                            </div>

                            @if($product->description)
                                <p style="font-size: 13px; color: var(--dim-white); line-height: 1.55; margin-bottom: 16px; flex-grow: 1;">
                                    {{ \Illuminate\Support\Str::limit($product->description, 95) }}
                                </p>
                            @endif

                            <div class="cinematic-vehicle-card__price">
                                &#8369;{{ number_format($product->price, 2) }}
                            </div>

                            <div class="cinematic-vehicle-card__actions">
                                <form action="{{ route('furever.add-cart', $product->id) }}" method="POST" style="flex: 1;">
                                    @csrf
                                    <button type="submit" class="cinematic-btn cinematic-btn--gold" style="width: 100%;" @disabled($product->stock <= 0)>
                                        <i class="fa fa-shopping-bag"></i>
                                        <span>{{ $product->stock > 0 ? 'Reserve / Cart' : 'Allocated' }}</span>
                                    </button>
                                </form>

                                @auth
                                    @if(auth()->user()->isFureverAdmin())
                                        <a href="{{ route('furever.products.edit', $product) }}" class="cinematic-btn cinematic-btn--ghost" title="Edit Vehicle">
                                            <i class="fa fa-pencil"></i>
                                        </a>
                                    @endif
                                @endauth
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="cinematic-panel" style="grid-column: 1 / -1; text-align: center; padding: 70px 20px;">
                        <i class="fa fa-car" style="font-size: 48px; color: var(--whisper); margin-bottom: 16px;"></i>
                        <h2 style="font-size: 20px; font-weight: 700; color: #FFFFFF; margin-bottom: 8px;">No vehicles match your criteria</h2>
                        <p style="font-size: 14px; color: var(--dim-white); margin-bottom: 24px;">Adjust your search or powertrain filters to view other atelier allocations.</p>
                        <a href="{{ route('furever.shop') }}" class="cinematic-btn cinematic-btn--gold">Clear All Filters</a>
                    </div>
                @endforelse
            </section>
        </div>
    </main>

    @include('pages.partials.tz-footer')
</x-layout>
