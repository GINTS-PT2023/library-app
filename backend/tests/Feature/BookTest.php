<?php

use App\Models\Book;

test('can list books', function () {
    Book::factory(5)->create();

    $response = $this->getJson('/api/books');

    $response->assertStatus(200)
        ->assertJsonCount(5);
});

test('can create a book', function () {
    $data = [
        'title' => 'The Great Gatsby',
        'author' => 'F. Scott Fitzgerald',
        'isbn' => '9780743273565',
        'published_year' => 1925,
        'total_copies' => 10,
        'available_copies' => 10,
    ];

    $response = $this->postJson('/api/books', $data);

    $response->assertStatus(201)
        ->assertJsonFragment($data);

    $this->assertDatabaseHas('books', $data);
});

test('can show a book', function () {
    $book = Book::factory()->create();

    $response = $this->getJson("/api/books/{$book->id}");

    $response->assertStatus(200)
        ->assertJsonFragment(['title' => $book->title]);
});

test('can update a book', function () {
    $book = Book::factory()->create();
    $newData = ['title' => 'Updated Title'];

    $response = $this->putJson("/api/books/{$book->id}", $newData);

    $response->assertStatus(200)
        ->assertJsonFragment($newData);

    $this->assertDatabaseHas('books', array_merge(['id' => $book->id], $newData));
});

test('can delete a book', function () {
    $book = Book::factory()->create();

    $response = $this->deleteJson("/api/books/{$book->id}");

    $response->assertStatus(204);
    $this->assertDatabaseMissing('books', ['id' => $book->id]);
});
