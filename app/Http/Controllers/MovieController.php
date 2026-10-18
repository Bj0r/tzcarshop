<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    // Display all movies
    public function index()
    {
        $data = DB::table('movies')->get();
        return view('pages.page1', compact('data'));
    }

    // Show Add Form
    public function show_add_form()
    {
        return view('pages.add-movie-form');
    }

    // Add movie
    public function do_add(Request $request)
    {
        $filename = null;
        if ($request->file('poster')) {
            $file = $request->file('poster');
            $filename = $file->getClientOriginalName();
            $file->move('_uploads', $filename);
        }

        DB::table('movies')->insert([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'genre' => $request->input('genre'),
            'release_year' => $request->input('release_year'),
            'rating' => $request->input('rating'),
            'poster' => $filename
        ]);

        return redirect(url('/p1'))->with('success', 'Movie added successfully!');
    }

    // Delete movie
    public function do_delete($id)
    {
        DB::table('movies')->where('id', $id)->delete();
        return redirect(url('/p1'))->with('success', 'Movie deleted!');
    }

    // Show Edit Form
    public function show_edit_form($id)
    {
        $data = DB::table('movies')->where('id', $id)->first();
        return view('pages.edit-movie-form', compact('data'));
    }

    // Update movie
    public function do_update(Request $request)
    {
        $filename = $request->input('old_poster');
        if ($request->file('poster')) {
            $file = $request->file('poster');
            $filename = $file->getClientOriginalName();
            $file->move('_uploads', $filename);
        }

        DB::table('movies')->where('id', $request->input('id'))->update([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'genre' => $request->input('genre'),
            'release_year' => $request->input('release_year'),
            'rating' => $request->input('rating'),
            'poster' => $filename
        ]);

        return redirect(url('/p1'))->with('success', 'Movie updated successfully!');
    }
}