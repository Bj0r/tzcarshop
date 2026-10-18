
@extends('layouts.app')

@section('title', 'Query Builder vs. Eloquent ORM')

@section('content')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12">
        <h2>Query Builder vs. Eloquent ORM</h2>

        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ url('/dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ url('/p2') }}">Books</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Query Comparison</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">

    <!-- SEARCH AND FILTER PANEL -->
    <div class="ibox">
        <div class="ibox-title">
            <h5>
                <i class="fa fa-filter"></i>
                Book Search and Filters
            </h5>
        </div>

        <div class="ibox-content">

            <form method="GET"
                  action="{{ url('/book-query-comparison') }}">

                <div class="row">

                    <!-- TITLE SEARCH -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Search Book Title</label>

                            <input
                                type="text"
                                name="search"
                                class="form-control"
                                placeholder="Enter book title..."
                                value="{{ $search }}"
                            >
                        </div>
                    </div>

                    <!-- STOCK THRESHOLD -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Stock Threshold</label>

                            <input
                                type="number"
                                name="stock_limit"
                                class="form-control"
                                min="0"
                                max="1000"
                                value="{{ old('stock_limit', $stockLimit) }}"
                            >

                            @error('stock_limit')
                                <small class="text-danger">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>
                    </div>

                    <!-- LOW STOCK CHECKBOX -->
                    <div class="col-md-2">
                        <label>Stock Filter</label>

                        <div class="form-check mt-2">
                            <input
                                type="checkbox"
                                class="form-check-input"
                                id="low_stock"
                                name="low_stock"
                                value="1"
                                {{ $lowStock ? 'checked' : '' }}
                            >

                            <label class="form-check-label"
                                   for="low_stock">
                                Below threshold
                            </label>
                        </div>
                    </div>

                    <!-- ACTION BUTTONS -->
                    <div class="col-md-3">
                        <label>Actions</label>

                        <div>
                            <button type="submit"
                                    class="btn btn-primary">
                                <i class="fa fa-search"></i>
                                Search
                            </button>

                            <a href="{{ url('/book-query-comparison') }}"
                               class="btn btn-default">
                                <i class="fa fa-refresh"></i>
                                Reset
                            </a>
                        </div>
                    </div>

                </div>
            </form>

            <!-- SELECTED THRESHOLD -->
            <div class="alert alert-info mb-0">
                <i class="fa fa-info-circle"></i>

                Selected Stock Threshold:
                <strong>{{ $stockLimit }}</strong>

                @if($lowStock)
                    — Showing books with stock below
                    {{ $stockLimit }}.
                @else
                    — Stock filtering is disabled.
                @endif
            </div>

        </div>
    </div>

    <!-- COMPARISON RESULTS -->
    <div class="row">

        <!-- QUERY BUILDER -->
        <div class="col-lg-6">

            <div class="ibox">
                <div class="ibox-title">
                    <h5>
                        <i class="fa fa-database"></i>
                        Laravel Query Builder
                    </h5>

                    <span class="label label-primary float-right">
                        {{ $queryBuilderBooks->count() }} Records
                    </span>
                </div>

                <div class="ibox-content">

                    @forelse($queryBuilderBooks as $book)

                        <div class="card mb-3">
                            <div class="card-body">

                                <h5 class="font-bold">
                                    {{ $book->title }}
                                </h5>

                                <p class="mb-1">
                                    <i class="fa fa-cubes"></i>
                                    Stock:
                                    <strong>{{ $book->stock }}</strong>
                                </p>

                                <p class="mb-0">
                                    <i class="fa fa-money"></i>
                                    Price:
                                    <strong>
                                        ₱{{ number_format($book->amount, 2) }}
                                    </strong>
                                </p>

                            </div>
                        </div>

                    @empty
                        <div class="alert alert-warning">
                            No books found.
                        </div>
                    @endforelse

                </div>
            </div>

        </div>

        <!-- ELOQUENT ORM -->
        <div class="col-lg-6">

            <div class="ibox">
                <div class="ibox-title">
                    <h5>
                        <i class="fa fa-code"></i>
                        Eloquent ORM
                    </h5>

                    <span class="label label-success float-right">
                        {{ $eloquentBooks->count() }} Records
                    </span>
                </div>

                <div class="ibox-content">

                    @forelse($eloquentBooks as $book)

                        <div class="card mb-3">
                            <div class="card-body">

                                <h5 class="font-bold">
                                    {{ $book->title }}
                                </h5>

                                <p class="mb-1">
                                    <i class="fa fa-cubes"></i>
                                    Stock:
                                    <strong>{{ $book->stock }}</strong>
                                </p>

                                <p class="mb-0">
                                    <i class="fa fa-money"></i>
                                    Price:
                                    <strong>
                                        ₱{{ number_format($book->amount, 2) }}
                                    </strong>
                                </p>

                            </div>
                        </div>

                    @empty
                        <div class="alert alert-warning">
                            No books found.
                        </div>
                    @endforelse

                </div>
            </div>

        </div>

    </div>

</div>

@endsection
