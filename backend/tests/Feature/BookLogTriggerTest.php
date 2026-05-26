<?php

namespace Tests\Feature;

use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class BookLogTriggerTest extends TestCase
{
    use RefreshDatabase;

    public function test_trigger_logs_available_copies_change()
    {
        $book = Book::factory()->create([
            'available_copies' => 5,
        ]);

        // Perform update via Eloquent
        $book->update(['available_copies' => 4]);

        $this->assertDatabaseHas('book_logs', [
            'book_id' => $book->id,
            'column_name' => 'available_copies',
            'old_value' => '5',
            'new_value' => '4',
        ]);
    }

    public function test_trigger_logs_total_copies_change()
    {
        $book = Book::factory()->create([
            'total_copies' => 10,
        ]);

        // Perform update via Direct SQL to ensure trigger works bypassing application logic
        DB::table('books')->where('id', $book->id)->update(['total_copies' => 12]);

        $this->assertDatabaseHas('book_logs', [
            'book_id' => $book->id,
            'column_name' => 'total_copies',
            'old_value' => '10',
            'new_value' => '12',
        ]);
    }

    public function test_trigger_does_not_log_if_no_change()
    {
        $book = Book::factory()->create([
            'available_copies' => 5,
        ]);

        // Update with same value
        $book->update(['available_copies' => 5]);

        $this->assertEquals(0, DB::table('book_logs')->count());
    }

    public function test_trigger_logs_multiple_changes_at_once()
    {
        $book = Book::factory()->create([
            'available_copies' => 5,
            'total_copies' => 5,
        ]);

        $book->update([
            'available_copies' => 4,
            'total_copies' => 6,
        ]);

        $this->assertDatabaseHas('book_logs', [
            'book_id' => $book->id,
            'column_name' => 'available_copies',
            'old_value' => '5',
            'new_value' => '4',
        ]);

        $this->assertDatabaseHas('book_logs', [
            'book_id' => $book->id,
            'column_name' => 'total_copies',
            'old_value' => '5',
            'new_value' => '6',
        ]);
    }
}
