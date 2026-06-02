<?php

use App\Models\Book;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- Task 3: Copying Records ---\n";

// Get an active book (global scope is on)
$originalBook = Book::first();

if (!$originalBook) {
    echo "No books found. Seeding...\n";
    \Illuminate\Support\Facades\Artisan::call('db:seed');
    $originalBook = Book::first();
}

echo "Original Book: [ID: {$originalBook->id}] '{$originalBook->title}'\n";

// Use the controller's logic (or simulate it)
$controller = new \App\Http\Controllers\BookController();
$copy = $controller->copy($originalBook);

echo "New Copy created: [ID: {$copy->id}] '{$copy->title}'\n";
echo "Copied from ID: {$copy->copied_from_id}\n";
echo "Copied at: {$copy->copied_at}\n";

if (str_starts_with($copy->title, 'Copy of ')) {
    echo "SUCCESS: Title correctly prepended with 'Copy of '.\n";
} else {
    echo "FAILURE: Title not prepended correctly.\n";
}

if ($copy->copied_from_id === $originalBook->id) {
    echo "SUCCESS: Original ID stored correctly.\n";
} else {
    echo "FAILURE: Original ID not stored correctly.\n";
}
