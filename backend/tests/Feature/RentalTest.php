<?php

use App\Models\Book;
use App\Models\Reader;
use App\Models\Rental;

test('can list rentals', function () {
    Rental::factory(5)->create();

    $response = $this->getJson('/api/rentals');

    $response->assertStatus(200)
        ->assertJsonCount(5);
});

test('can create a rental', function () {
    $book = Book::factory()->create();
    $reader = Reader::factory()->create();
    $data = [
        'book_id' => $book->id,
        'reader_id' => $reader->id,
        'rented_at' => now()->toDateTimeString(),
        'due_at' => now()->addDays(14)->toDateTimeString(),
    ];

    $response = $this->postJson('/api/rentals', $data);

    $response->assertStatus(201);
    $this->assertDatabaseHas('rentals', [
        'book_id' => $book->id,
        'reader_id' => $reader->id,
    ]);
});

test('can show a rental', function () {
    $rental = Rental::factory()->create();

    $response = $this->getJson("/api/rentals/{$rental->id}");

    $response->assertStatus(200)
        ->assertJsonStructure(['id', 'book', 'reader']);
});

test('can update a rental', function () {
    $rental = Rental::factory()->create();
    $newData = ['returned_at' => now()->toDateTimeString()];

    $response = $this->putJson("/api/rentals/{$rental->id}", $newData);

    $response->assertStatus(200);
    $this->assertDatabaseHas('rentals', [
        'id' => $rental->id,
        'returned_at' => $newData['returned_at'],
    ]);
});

test('can delete a rental', function () {
    $rental = Rental::factory()->create();

    $response = $this->deleteJson("/api/rentals/{$rental->id}");

    $response->assertStatus(204);
    $this->assertDatabaseMissing('rentals', ['id' => $rental->id]);
});
