<?php

use App\Models\Book;
use App\Models\Reader;
use App\Models\Comment;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- Task 1: Polymorphic Relationships ---\n";

$book = Book::first();
$reader = Reader::first();

if (!$book || !$reader) {
    echo "Error: No data found. Please run migrations and seeders.\n";
    exit(1);
}

// Add comments to a book
$book->comments()->create(['body' => 'Great book for beginners!']);
$book->comments()->create(['body' => 'I loved the ending.']);

// Add comments to a reader
$reader->comments()->create(['body' => 'Reliable reader, always returns on time.']);

echo "Book '{$book->title}' has " . $book->comments()->count() . " comments.\n";
foreach ($book->comments as $comment) {
    echo " - " . $comment->body . "\n";
}

echo "Reader '{$reader->first_name} {$reader->last_name}' has " . $reader->comments()->count() . " comments.\n";
foreach ($reader->comments as $comment) {
    echo " - " . $comment->body . "\n";
}

echo "\n--- Task 2: N+1 Problem Exploration ---\n";

echo "Simulating N+1 problem (Lazy Loading):\n";
$books = Book::limit(5)->get();
$queryCount = 0;
DB::listen(function ($query) use (&$queryCount) {
    $queryCount++;
});

foreach ($books as $b) {
    // This triggers a query for each book's comments
    $count = $b->comments->count();
}
echo "Executed $queryCount queries for " . count($books) . " books (Lazy Loading).\n";

echo "\nSolving N+1 problem (Eager Loading):\n";
$queryCount = 0;
// We use withoutGlobalScope to make sure we get enough books if some are inactive
$booksEager = Book::withoutGlobalScope('onlyActive')->with('comments')->limit(5)->get();
foreach ($booksEager as $b) {
    $count = $b->comments->count();
}
echo "Executed $queryCount queries for " . count($booksEager) . " books (Eager Loading).\n";

echo "\n--- Task 3: Eloquent Scopes ---\n";

// Using local scope
echo "Using local scope 'withComments':\n";
$bookWithScope = Book::withComments()->first();
echo "Book '{$bookWithScope->title}' comments loaded: " . ($bookWithScope->relationLoaded('comments') ? 'Yes' : 'No') . "\n";

// Global scope demonstration
echo "Global scope 'onlyActive' is active by default.\n";
$totalBooks = Book::withoutGlobalScope('onlyActive')->count();
$activeBooks = Book::count();
echo "Total books (ignoring global scope): $totalBooks\n";
echo "Active books (with global scope): $activeBooks\n";

echo "\n--- Task 4: Soft Deletes ---\n";

$bookToDelete = Book::first();
echo "Deleting book: {$bookToDelete->title}\n";
$bookToDelete->delete();

echo "Book exists in database (withTrashed): " . (Book::withTrashed()->find($bookToDelete->id) ? 'Yes' : 'No') . "\n";
echo "Book found by regular query: " . (Book::find($bookToDelete->id) ? 'Yes' : 'No') . "\n";

if ($bookToDelete->trashed()) {
    echo "[FLASH] The record for '{$bookToDelete->title}' has been deleted.\n";
}

echo "\n--- Task 5: Archiving/Flagging ---\n";

$bookToArchive = Book::where('status', 'active')->first();
echo "Archiving book: {$bookToArchive->title}\n";
$bookToArchive->status = 'archived';
$bookToArchive->save();

echo "Book found in default (active) query: " . (Book::find($bookToArchive->id) ? 'Yes' : 'No') . "\n";
echo "Book found in 'archived' local scope: " . (Book::archived()->where('id', $bookToArchive->id)->exists() ? 'Yes' : 'No') . "\n";
echo "Book found when ignoring global scope: " . (Book::withoutGlobalScope('onlyActive')->find($bookToArchive->id) ? 'Yes' : 'No') . "\n";
