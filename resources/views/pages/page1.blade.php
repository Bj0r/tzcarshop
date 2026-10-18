@extends('layouts.app')

@section('content')

<x-page-header pagetitle="Movies" class="bg-primary"/>

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ url('/dashboard') }}">Home</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Movies</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">

    @if(Session::has('success'))
        <p class="alert alert-success">{{ Session::get('success') }}</p>
    @endif

    <a href="{{ url('/movie-form') }}" class="btn btn-primary mb-3">
        <i class="fa fa-plus"></i> Add Movie
    </a>

    <div class="row">
        @foreach($data as $d)
        <div class="col-md-3 mb-4">
            <div class="card">
                <img class="card-img-top" src="{{ asset('_uploads/'.$d->poster) }}" alt="Movie Poster" style="width:100%">
                <div class="card-body">
                    <h5 class="card-title">{{ $d->title }}</h5>
                    <p class="card-text">
                        Genre: {{ $d->genre }}<br>
                        Year: {{ $d->release_year }}<br>
                        Rating: <span class="label label-primary">{{ $d->rating }}/10</span>
                    </p>
                    <a href="{{ url('/edit-movie/'.$d->id) }}" class="btn btn-info btn-sm" onclick="return confirm('Update this movie?')"><i class="fa fa-edit"></i></a>
                    <a href="{{ url('/delete-movie/'.$d->id) }}" class="btn btn-danger btn-sm" onclick="return confirm('Delete this movie?')"><i class="fa fa-trash"></i></a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

</div>

@endsection