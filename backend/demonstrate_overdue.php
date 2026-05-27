<?php

use App\Models\Book;
use App\Models\Reader;
use App\Models\Rental;
use App\Models\OverdueRental;
use Illuminate\Support\Carbon;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Step 1: Creating a book and a reader...\n";
$book = Book::first() ?? Book::factory()->create(['title' => 'The Overdue Book', 'author' => 'John Doe']);
$reader = Reader::first() ?? Reader::factory()->create(['first_name' => 'Jane', 'last_name' => 'Smith']);

echo "Step 2: Manually entering a loan with a date in the past (Overdue)...\n";
$pastDate = Carbon::now()->subDays(10);
$dueDate = Carbon::now()->subDays(3);

$rental = Rental::create([
    'book_id' => $book->id,
    'reader_id' => $reader->id,
    'rented_at' => $pastDate,
    'due_at' => $dueDate,
    'returned_at' => null,
]);

echo "Rental created with ID: {$rental->id}, Due at: {$rental->due_at->toDateTimeString()}\n";

echo "\nStep 3: Opening the prepared view (OverdueRental model)...\n";
$overdueRentals = OverdueRental::all();

echo "Found " . $overdueRentals->count() . " overdue rentals in the view.\n";

foreach ($overdueRentals as $overdue) {
    if ($overdue->rental_id == $rental->id) {
        echo "SUCCESS: Found our newly created overdue rental in the view!\n";
        echo "Book: {$overdue->book_title} by {$overdue->book_author}\n";
        echo "Reader: {$overdue->reader_name}\n";
        echo "Due at: {$overdue->due_at->toDateTimeString()}\n";
    }
}
