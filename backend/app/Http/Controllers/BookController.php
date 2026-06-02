<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Book::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $driver = $query->getConnection()->getDriverName();

            // Use Full-Text search if the driver is not SQLite (supports MySQL/PostgreSQL)
            if ($driver !== 'sqlite') {
                $query->whereFullText(['title', 'author'], $search);
            } else {
                // Fallback to LIKE for SQLite or other drivers without FULLTEXT index
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'LIKE', "%{$search}%")
                      ->orWhere('author', 'LIKE', "%{$search}%")
                      ->orWhere('isbn', 'LIKE', "%{$search}%");
                });
            }
        }

        return $query->paginate($request->input('per_page', 10));
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

    public function copy(Book $book)
    {
        $copy = $book->replicate();
        $copy->title = 'Copy of ' . $book->title;
        $copy->isbn = $book->isbn . '-COPY-' . now()->timestamp; // Avoid unique constraint
        $copy->copied_from_id = $book->id;
        $copy->copied_at = now();
        $copy->save();

        return $copy;
    }
}
