@extends('layouts.app')
@section('title', $heading)
@section('content')
<x-page-header pagetitle="{{ $heading }}" class="bg-info"/>
<div class="wrapper wrapper-content">
    <div class="ibox"><div class="ibox-content">
        <h3>{{ $heading }}</h3>
        <p class="text-muted">Not built yet.</p>
    </div></div>
</div>
@endsection