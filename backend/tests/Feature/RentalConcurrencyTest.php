<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Reader;
use App\Models\Rental;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RentalConcurrencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_cannot_rent_if_no_copies_available()
    {
        $book = Book::factory()->create([
            'total_copies' => 1,
            'available_copies' => 0,
        ]);
        $reader = Reader::factory()->create();

        $response = $this->postJson('/api/rentals', [
            'book_id' => $book->id,
            'reader_id' => $reader->id,
            'rented_at' => now()->toDateTimeString(),
            'due_at' => now()->addDays(7)->toDateTimeString(),
        ]);

        $response->assertStatus(422);
        $response->assertJson(['message' => 'No copies available']);
        $this->assertEquals(0, Rental::count());
    }

    public function test_available_copies_decremented_on_rental()
    {
        $book = Book::factory()->create([
            'total_copies' => 5,
            'available_copies' => 5,
        ]);
        $reader = Reader::factory()->create();

        $this->postJson('/api/rentals', [
            'book_id' => $book->id,
            'reader_id' => $reader->id,
            'rented_at' => now()->toDateTimeString(),
            'due_at' => now()->addDays(7)->toDateTimeString(),
        ]);

        $this->assertEquals(4, $book->fresh()->available_copies);
    }

    public function test_available_copies_incremented_on_return()
    {
        $book = Book::factory()->create([
            'total_copies' => 5,
            'available_copies' => 4,
        ]);
        $reader = Reader::factory()->create();
        $rental = Rental::factory()->create([
            'book_id' => $book->id,
            'reader_id' => $reader->id,
            'returned_at' => null,
        ]);

        $this->putJson("/api/rentals/{$rental->id}", [
            'returned_at' => now()->toDateTimeString(),
        ]);

        $this->assertEquals(5, $book->fresh()->available_copies);
    }

    public function test_return_only_increments_once()
    {
        $book = Book::factory()->create([
            'total_copies' => 5,
            'available_copies' => 4,
        ]);
        $reader = Reader::factory()->create();
        $rental = Rental::factory()->create([
            'book_id' => $book->id,
            'reader_id' => $reader->id,
            'returned_at' => null,
        ]);

        // First return
        $this->putJson("/api/rentals/{$rental->id}", [
            'returned_at' => now()->toDateTimeString(),
        ]);
        $this->assertEquals(5, $book->fresh()->available_copies);

        // Second return update (e.g. changing the date)
        $this->putJson("/api/rentals/{$rental->id}", [
            'returned_at' => now()->subHour()->toDateTimeString(),
        ]);
        $this->assertEquals(5, $book->fresh()->available_copies);
    }
}
