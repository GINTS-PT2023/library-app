<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Reader;
use App\Models\Rental;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Rental>
 */
class RentalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $rentedAt = $this->faker->dateTimeBetween('-1 month', 'now');
        $dueAt = (clone $rentedAt)->modify('+14 days');
        $returnedAt = $this->faker->optional(0.7)->dateTimeBetween($rentedAt, '+21 days');

        return [
            'book_id' => Book::factory(),
            'reader_id' => Reader::factory(),
            'rented_at' => $rentedAt,
            'due_at' => $dueAt,
            'returned_at' => $returnedAt,
        ];
    }
}
