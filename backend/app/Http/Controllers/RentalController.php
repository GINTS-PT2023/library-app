<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RentalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Rental::with(['book', 'reader'])->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'reader_id' => 'required|exists:readers,id',
            'rented_at' => 'required|date',
            'due_at' => 'required|date|after:rented_at',
            'returned_at' => 'nullable|date|after_or_equal:rented_at',
        ]);

        try {
            return DB::transaction(function () use ($validated) {
                $book = Book::lockForUpdate()->findOrFail($validated['book_id']);

                if ($book->available_copies <= 0) {
                    return response()->json(['message' => 'No copies available'], 422);
                }

                $book->decrement('available_copies');
                
                return Rental::create($validated);
            });
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to create rental'], 500);
        }
    }

    public function show(Rental $rental)
    {
        return $rental->load(['book', 'reader']);
    }

    public function update(Request $request, Rental $rental)
    {
        $validated = $request->validate([
            'book_id' => 'exists:books,id',
            'reader_id' => 'exists:readers,id',
            'rented_at' => 'date',
            'due_at' => 'date|after:rented_at',
            'returned_at' => 'nullable|date|after_or_equal:rented_at',
        ]);

        return DB::transaction(function () use ($validated, $rental) {
            // Check if it's being returned for the first time
            if (isset($validated['returned_at']) && is_null($rental->returned_at)) {
                $rental->book->increment('available_copies');
            }

            $rental->update($validated);
            return $rental;
        });
    }

    public function destroy(Rental $rental)
    {
        $rental->delete();

        return response()->noContent();
    }
}
