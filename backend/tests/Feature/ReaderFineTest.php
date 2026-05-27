<?php

use App\Models\Book;
use App\Models\Reader;
use App\Models\Rental;
use App\Models\ReaderFine;
use Illuminate\Support\Carbon;

test('reader fine is correctly calculated in the database', function () {
    $reader = Reader::factory()->create();
    $book = Book::factory()->create();

    // 5 days late (currently overdue)
    Rental::create([
        'book_id' => $book->id,
        'reader_id' => $reader->id,
        'rented_at' => Carbon::now()->subDays(10),
        'due_at' => Carbon::now()->subDays(5),
        'returned_at' => null,
    ]);

    // 2 days late (already returned)
    Rental::create([
        'book_id' => $book->id,
        'reader_id' => $reader->id,
        'rented_at' => Carbon::now()->subDays(10),
        'due_at' => Carbon::now()->subDays(8),
        'returned_at' => Carbon::now()->subDays(6),
    ]);

    // Not late
    Rental::create([
        'book_id' => $book->id,
        'reader_id' => $reader->id,
        'rented_at' => Carbon::now()->subDays(1),
        'due_at' => Carbon::now()->addDays(5),
        'returned_at' => null,
    ]);

    // Total late days: 5 + 2 = 7
    // Fine: 7 * 0.50 = 3.50

    $fine = ReaderFine::where('reader_id', $reader->id)->value('total_fine');

    expect((float)$fine)->toBe(3.50);
});
