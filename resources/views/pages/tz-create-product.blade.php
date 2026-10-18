<x-layout>
    @push('styles')<link rel="stylesheet" href="{{ asset('css/tz.css') }}">@endpush

    <div class="tz-page">
        @include('pages.partials.tz-admin-sidebar')
        <main class="tz-form-page">
            <header class="tz-form-heading">
                <a href="{{ route('furever.products') }}" class="tz-back-link"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to inventory</a>
                <span class="tz-eyebrow">TZ ATELIER &bull; Vehicle management</span>
                <h1>Add New Car</h1>
                <p>Add a Toyota vehicle to the TZ ATELIER inventory.</p>
            </header>

            @if($errors->any())
                <div class="tz-form-errors" role="alert">
                    <strong>Please check the vehicle information.</strong>
                    <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            <form action="{{ route('furever.products.store') }}" method="POST" enctype="multipart/form-data" class="tz-form-card">
                @csrf
                <div class="tz-form-grid">
                    <div class="tz-field tz-field--wide">
                        <label for="name">Model name</label>
                        <select id="name" name="name" required>
                            <option value="">Select a Toyota model</option>
                            @foreach(['Toyota Vios', 'Toyota Wigo', 'Toyota Raize', 'Toyota Hilux', 'Toyota Fortuner', 'Toyota Innova', 'Toyota Corolla Cross', 'Toyota Land Cruiser'] as $model)
                                <option value="{{ $model }}" @selected(old('name') === $model)>{{ $model }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="tz-field"><label for="year">Year</label><input id="year" type="number" name="year" min="1990" max="2100" value="{{ old('year', date('Y')) }}" required></div>
                    <div class="tz-field"><label for="price">Price (PHP)</label><input id="price" type="number" name="price" min="0" step="0.01" value="{{ old('price') }}" placeholder="0.00" required></div>
                    <div class="tz-field"><label for="color">Color</label><input id="color" type="text" name="color" value="{{ old('color') }}" placeholder="e.g. Silver Metallic" required></div>
                    <div class="tz-field">
                        <label for="transmission">Transmission</label>
                        <select id="transmission" name="transmission" required>@foreach(['Manual', 'Automatic', 'CVT'] as $value)<option value="{{ $value }}" @selected(old('transmission') === $value)>{{ $value }}</option>@endforeach</select>
                    </div>
                    <div class="tz-field">
                        <label for="fuel_type">Fuel type</label>
                        <select id="fuel_type" name="fuel_type" required>@foreach(['Gasoline', 'Diesel', 'Hybrid'] as $value)<option value="{{ $value }}" @selected(old('fuel_type') === $value)>{{ $value }}</option>@endforeach</select>
                    </div>
                    <div class="tz-field"><label for="stock">Stock quantity</label><input id="stock" type="number" name="stock" min="0" value="{{ old('stock', 0) }}" required></div>
                    <div class="tz-field tz-field--wide"><label for="image_url">Image URL</label><input id="image_url" type="url" name="image_url" value="{{ old('image_url') }}" placeholder="https://example.com/toyota.jpg"><small>Use a direct HTTPS image URL, or upload an image below.</small></div>
                    <div class="tz-field tz-field--wide"><label for="image">Upload image</label><input id="image" type="file" name="image" accept="image/jpeg,image/png,image/webp"><small>JPG, PNG, or WEBP up to 2 MB.</small></div>
                    <div class="tz-field tz-field--wide"><label for="description">Description</label><textarea id="description" name="description" rows="5" placeholder="Describe this Toyota vehicle...">{{ old('description') }}</textarea></div>
                </div>
                <div class="tz-form-actions">
                    <a href="{{ route('furever.products') }}" class="tz-button tz-button--outline">Cancel</a>
                    <button type="submit" class="tz-button tz-button--primary"><i class="fa fa-save" aria-hidden="true"></i> Save Car</button>
                </div>
            </form>
        </main>
    </div>
</x-layout>
