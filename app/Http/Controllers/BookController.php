<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BookController extends Controller
{


private function writeActivityLog(
    string $action,
    ?int $bookId,
    string $description,
    Request $request
): void {
    // Standard Laravel application log
    Log::info('Book activity', [
        'action' => $action,
        'book_id' => $bookId,
        'description' => $description,
        'ip_address' => $request->ip(),
        'http_method' => $request->method(),
        'url' => $request->path(),
    ]);

    // Structured audit record in the database
    DB::table('activity_logs')->insert([
        'user_id' => null,
        'action' => $action,
        'entity_type' => 'Book',
        'entity_id' => $bookId,
        'description' => $description,
        'ip_address' => $request->ip(),
        'http_method' => $request->method(),
        'url' => $request->path(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

    // Display all books
    public function index()
    {
        $data = DB::table('books')->get();
        return view('pages.page2', compact('data'));
    }

    // Show Add Form
    public function show_add_form()
    {
        return view('pages.add-book-form');
    }

    // Add book

    // Add book
    public function do_add(Request $request)
    {
        $filename = null;

        if ($request->file('photo')) {
            $file = $request->file('photo');
            $filename = $file->getClientOriginalName();
            $file->move('_uploads', $filename);
        }

        $bookId = DB::table('books')->insertGetId([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'country_id' => $request->input('country_id'),
            'stock' => $request->input('stock'),
            'amount' => $request->input('amount'),
            'photo' => $filename,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->writeActivityLog(
            'BOOK_CREATED',
            (int) $bookId,
            'A book record was created.',
            $request
        );

        return redirect(url('/p2'))
            ->with('success', 'Book added successfully!');
    }


    // Delete book
    public function do_delete($id)
    {
        DB::table('books')->where('id', $id)->delete();
        return redirect(url('/p2'))->with('success', 'Book deleted!');
    }

    // Show Edit Form
    public function show_edit_form($id)
    {
        $data = DB::table('books')->where('id', $id)->first();
        return view('pages.edit-book-form', compact('data'));
    }

    // Update book

    // Update book
    public function do_update(Request $request)
    {
        // Get the book ID
        $bookId = (int) $request->input('id');

        // Check if the book exists
        $bookExists = DB::table('books')
            ->where('id', $bookId)
            ->exists();

        if (! $bookExists) {
            Log::warning('Book update attempted for a missing record', [
                'book_id' => $bookId,
                'ip_address' => $request->ip(),
            ]);

            abort(404, 'Book not found.');
        }

        // Keep existing photo or upload a new one
        $filename = $request->input('old_photo');

        if ($request->file('photo')) {
            $file = $request->file('photo');
            $filename = $file->getClientOriginalName();
            $file->move('_uploads', $filename);
        }

        // Update the book
        DB::table('books')
            ->where('id', $bookId)
            ->update([
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'country_id' => $request->input('country_id'),
                'stock' => $request->input('stock'),
                'amount' => $request->input('amount'),
                'photo' => $filename,
            ]);

        // Record the update activity
        $this->writeActivityLog(
            'BOOK_UPDATED',
            $bookId,
            'A book record was updated.',
            $request
        );

        return redirect(url('/p2'))
            ->with('success', 'Book updated successfully!');
    }

}