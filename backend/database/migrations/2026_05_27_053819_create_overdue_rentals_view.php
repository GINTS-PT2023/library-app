<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("DROP VIEW IF EXISTS overdue_rentals");
        
        // Using SQLite syntax for concatenation (||) as per the project's default configuration.
        // For MySQL, it would be CONCAT(readers.first_name, ' ', readers.last_name).
        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver");

        $readerNameSql = $driver === 'sqlite' 
            ? "readers.first_name || ' ' || readers.last_name" 
            : "CONCAT(readers.first_name, ' ', readers.last_name)";

        DB::statement("
            CREATE VIEW overdue_rentals AS
            SELECT 
                rentals.id AS rental_id,
                books.id AS book_id,
                books.title AS book_title,
                books.author AS book_author,
                readers.id AS reader_id,
                {$readerNameSql} AS reader_name,
                readers.email AS reader_email,
                rentals.rented_at,
                rentals.due_at
            FROM rentals
            JOIN books ON rentals.book_id = books.id
            JOIN readers ON rentals.reader_id = readers.id
            WHERE rentals.returned_at IS NULL AND rentals.due_at < CURRENT_TIMESTAMP
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS overdue_rentals");
    }
};
