@extends('layouts.app')

@section('content')
<x-page-header pagetitle="Edit Movie" class="bg-warning"/>

<div class="wrapper wrapper-content">
    <div class="animated fadeInRightBig">
        <form action="{{ url('/update-movie') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <input type="hidden" name="id" value="{{ $data->id }}">
                    <input type="hidden" name="old_poster" value="{{ $data->poster }}">
                    <div class="form-group mb-3">
                        <label for="title">Title</label>
                        <input type="text" class="form-control" name="title" id="title" required value="{{ $data->title }}">
                    </div>
                    <div class="form-group mb-3">
                        <label for="description">Description</label>
                        <textarea class="form-control" name="description" id="description">{{ $data->description }}</textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label for="genre">Genre</label>
                        <input type="text" class="form-control" name="genre" id="genre" value="{{ $data->genre }}">
                    </div>
                    <div class="form-group mb-3">
                        <label for="release_year">Release Year</label>
                        <input type="number" class="form-control" name="release_year" id="release_year" value="{{ $data->release_year }}">
                    </div>
                    <div class="form-group mb-3">
                        <label for="rating">Rating (0–10)</label>
                        <input type="number" step="0.1" min="0" max="10" class="form-control" name="rating" id="rating" value="{{ $data->rating }}">
                    </div>
                    <div class="form-group mb-3">
                        <label for="poster">Poster</label>
                        <input type="file" class="form-control" name="poster" id="poster">
                        <small class="text-muted">Current:</small><br>
                        <img src="{{ asset('_uploads/'.$data->poster) }}" width="100" class="border mt-1">
                    </div>
                    <hr>
                    <button class="btn btn-primary" type="submit">Update</button>
                    <a href="{{ url('/p1') }}" class="btn btn-secondary">Back</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection