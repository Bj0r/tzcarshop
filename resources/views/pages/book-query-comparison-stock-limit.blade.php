{{-- Part F version of book-query-comparison.blade.php.
     Differences from the original are marked with "DIFFERENT:". --}}
@extends('layouts.app')

@section('content')
{{-- DIFFERENT: page title --}}
<x-page-header pagetitle="Query Builder vs. Eloquent — Stock Threshold" class="bg-info"/>

<div class="container py-4">
    {{-- DIFFERENT: form submits to /book-query-comparison-stock-limit --}}
    <form method="GET" action="{{ url('/book-query-comparison-stock-limit') }}"
          class="row g-2 mb-4">
        {{-- DIFFERENT: was col-md-6, made smaller to fit the threshold box --}}
        <div class="col-md-4">
            <input type="text" name="search" class="form-control"
                   placeholder="Search book title..." value="{{ $search }}">
        </div>
        {{-- DIFFERENT: new number input named stock_limit (0 to 1000).
             @error shows the validation message if the value is invalid. --}}
        <div class="col-md-2">
            <input type="number" name="stock_limit" class="form-control"
                   min="0" max="1000" placeholder="Stock threshold"
                   value="{{ $stockLimit }}">
            @error('stock_limit')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
        <div class="col-md-3">
            <label class="form-check mt-2">
                <input class="form-check-input" type="checkbox"
                       name="low_stock" value="1"
                       {{ $lowStock ? 'checked' : '' }}>
                {{-- DIFFERENT: was "Stock below 10" --}}
                <span class="form-check-label">Stock below {{ $stockLimit }}</span>
            </label>
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary">Search</button>
            {{-- DIFFERENT: Reset goes back to this page --}}
            <a href="{{ url('/book-query-comparison-stock-limit') }}"
               class="btn btn-secondary">Reset</a>
        </div>
    </form>

    {{-- DIFFERENT: new line showing the selected threshold --}}
    <p>Selected stock threshold: <strong>{{ $stockLimit }}</strong></p>

    <div class="row">
        <div class="col-lg-6 mb-4">
            <h4>Laravel Query Builder</h4>
            <p>Records: {{ $queryBuilderBooks->count() }}</p>
            @forelse($queryBuilderBooks as $book)
                <div class="card mb-2"><div class="card-body">
                    <h5>{{ $book->title }}</h5>
                    <p class="mb-0">Stock: {{ $book->stock }}</p>
                    <p class="mb-0">Price: ₱{{ number_format($book->amount, 2) }}</p>
                </div></div>
            @empty
                <p>No books found.</p>
            @endforelse
        </div>

        <div class="col-lg-6 mb-4">
            <h4>Eloquent ORM</h4>
            <p>Records: {{ $eloquentBooks->count() }}</p>
            @forelse($eloquentBooks as $book)
                <div class="card mb-2"><div class="card-body">
                    <h5>{{ $book->title }}</h5>
                    <p class="mb-0">Stock: {{ $book->stock }}</p>
                    <p class="mb-0">Price: ₱{{ number_format($book->amount, 2) }}</p>
                </div></div>
            @empty
                <p>No books found.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
