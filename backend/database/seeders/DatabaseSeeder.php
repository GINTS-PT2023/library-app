<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Reader;
use App\Models\Rental;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $books = Book::factory(50)->create();
        $readers = Reader::factory(20)->create();

        Rental::factory(30)->recycle($books)->recycle($readers)->create();
    }
}
