<x-layout>
    <x-slot:title>TZ ATELIER — Customer Management</x-slot:title>

    @include('pages.partials.tz-admin-sidebar')

    <main class="cinematic-subpage-wrap fur-storefront">
        <div class="cinematic-subpage-container fur-shell fur-admin-page">
            <header class="cinematic-subpage-header fur-admin-heading">
                <div>
                    <span class="cinematic-subpage-eyebrow fur-eyebrow fur-eyebrow--orange">Customer management</span>
                    <h1 class="cinematic-subpage-title">View customers and manage account status</h1>
                    <p class="cinematic-subpage-subtitle">Customer records are loaded directly from the users database table.</p>
                </div>
            </header>

            @if(session('success'))
                <div class="cinematic-panel fur-message fur-message--success" style="padding: 16px 20px; margin-bottom: 24px; border-color: rgba(45, 138, 94, 0.4); background: rgba(45, 138, 94, 0.1);">
                    <span style="color: #4ade80; font-size: 13px; font-weight: 600;">
                        <i class="fa fa-check-circle"></i> {{ session('success') }}
                    </span>
                </div>
            @endif

            {{-- CLIENT STAT DECK --}}
            <section class="cinematic-stat-deck fur-admin-stat-grid" aria-label="Client statistics">
                <article class="cinematic-stat-card fur-card">
                    <span class="cinematic-stat-label">Total customers</span>
                    <strong class="cinematic-stat-value">{{ $totalCustomers }}</strong>
                </article>
                <article class="cinematic-stat-card fur-card">
                    <span class="cinematic-stat-label">Active customers</span>
                    <strong class="cinematic-stat-value">{{ $activeCustomers }}</strong>
                </article>
                <article class="cinematic-stat-card fur-card">
                    <span class="cinematic-stat-label">Inactive customers</span>
                    <strong class="cinematic-stat-value">{{ $totalCustomers - $activeCustomers }}</strong>
                </article>
            </section>

            {{-- CLIENT DIRECTORY PANEL --}}
            <section class="cinematic-panel fur-card fur-customer-table-card">
                <div class="cinematic-panel-header fur-customer-table-card__header">
                    <div>
                        <h2 class="cinematic-panel-title">Customer directory</h2>
                        <p style="font-size: 13px; color: var(--dim-white); margin-top: 4px;">Search a customer, then update their account status.</p>
                    </div>

                    <form action="{{ route('furever.customers') }}" method="GET" class="cinematic-filter-bar fur-admin-search" style="padding: 0; border: none; background: none; margin-bottom: 0;">
                        <div class="cinematic-search-input-wrap">
                            <i class="fa fa-search" aria-hidden="true"></i>
                            <input type="search" name="search" value="{{ request('search') }}" placeholder="Search customers..." aria-label="Search customers">
                        </div>
                        <button type="submit" class="cinematic-btn cinematic-btn--gold" style="padding: 10px 18px;">Search</button>
                    </form>
                </div>

                <div class="cinematic-table-wrap fur-customer-table-wrap">
                    <table class="cinematic-table fur-customer-table">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Joined</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customers as $customer)
                                <tr>
                                    <td><strong style="color: #FFFFFF;">{{ $customer->name }}</strong></td>
                                    <td style="color: var(--dim-white);">{{ $customer->username }}</td>
                                    <td style="color: var(--dim-white);">{{ $customer->email }}</td>
                                    <td style="color: var(--dim-white);">{{ $customer->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <form action="{{ route('furever.customers.status', $customer) }}" method="POST" class="fur-status-form">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" onchange="this.form.submit()" aria-label="Update status for {{ $customer->name }}" class="cinematic-filter-select" style="padding: 6px 12px; font-size: 12px;">
                                                <option value="active" @selected($customer->status === 'active')>Active</option>
                                                <option value="inactive" @selected($customer->status === 'inactive')>Inactive</option>
                                            </select>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="fur-customer-table__empty" style="text-align: center; padding: 40px 0; color: var(--dim-white);">No customer records match your search.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </main>
</x-layout>
