<?php

use App\Models\Book;
use App\Models\Reader;
use App\Models\Rental;
use App\Models\OverdueRental;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('overdue rentals view contains only overdue and unreturned rentals', function () {
    $book = Book::factory()->create(['title' => 'Overdue Book']);
    $reader = Reader::factory()->create(['first_name' => 'John', 'last_name' => 'Doe']);

    // 1. Overdue and not returned
    Rental::factory()->create([
        'book_id' => $book->id,
        'reader_id' => $reader->id,
        'due_at' => now()->subDays(5),
        'returned_at' => null,
    ]);

    // 2. Not overdue and not returned
    Rental::factory()->create([
        'due_at' => now()->addDays(5),
        'returned_at' => null,
    ]);

    // 3. Overdue but returned
    Rental::factory()->create([
        'due_at' => now()->subDays(5),
        'returned_at' => now()->subDays(2),
    ]);

    $overdueRentals = OverdueRental::all();

    expect($overdueRentals)->toHaveCount(1);
    expect($overdueRentals->first()->book_title)->toBe('Overdue Book');
    expect($overdueRentals->first()->reader_name)->toBe('John Doe');

    // Test API endpoint
    $response = $this->getJson('/api/rentals/overdue');
    $response->assertStatus(200)
        ->assertJsonCount(1)
        ->assertJsonFragment(['book_title' => 'Overdue Book', 'reader_name' => 'John Doe']);
});
