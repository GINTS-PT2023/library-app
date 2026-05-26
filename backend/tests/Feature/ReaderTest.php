<?php

use App\Models\Reader;

test('can list readers', function () {
    Reader::factory(5)->create();

    $response = $this->getJson('/api/readers');

    $response->assertStatus(200)
        ->assertJsonCount(5);
});

test('can create a reader', function () {
    $data = [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@example.com',
        'phone_number' => '123456789',
        'address' => '123 Main St',
    ];

    $response = $this->postJson('/api/readers', $data);

    $response->assertStatus(201)
        ->assertJsonFragment($data);

    $this->assertDatabaseHas('readers', $data);
});

test('can show a reader', function () {
    $reader = Reader::factory()->create();

    $response = $this->getJson("/api/readers/{$reader->id}");

    $response->assertStatus(200)
        ->assertJsonFragment(['email' => $reader->email]);
});

test('can update a reader', function () {
    $reader = Reader::factory()->create();
    $newData = ['first_name' => 'Jane'];

    $response = $this->putJson("/api/readers/{$reader->id}", $newData);

    $response->assertStatus(200)
        ->assertJsonFragment($newData);

    $this->assertDatabaseHas('readers', array_merge(['id' => $reader->id], $newData));
});

test('can delete a reader', function () {
    $reader = Reader::factory()->create();

    $response = $this->deleteJson("/api/readers/{$reader->id}");

    $response->assertStatus(204);
    $this->assertDatabaseMissing('readers', ['id' => $reader->id]);
});
