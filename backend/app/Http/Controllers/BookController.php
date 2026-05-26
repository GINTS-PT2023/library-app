<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Book::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'author' => 'required|string',
            'isbn' => 'required|string|unique:books',
            'published_year' => 'required|integer',
            'total_copies' => 'required|integer|min:0',
            'available_copies' => 'required|integer|min:0',
        ]);

        return Book::create($validated);
    }

    public function show(Book $book)
    {
        return $book;
    }

    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title' => 'string',
            'author' => 'string',
            'isbn' => 'string|unique:books,isbn,'.$book->id,
            'published_year' => 'integer',
            'total_copies' => 'integer|min:0',
            'available_copies' => 'integer|min:0',
        ]);

        $book->update($validated);

        return $book;
    }

    public function destroy(Book $book)
    {
        $book->delete();

        return response()->noContent();
    }
}
