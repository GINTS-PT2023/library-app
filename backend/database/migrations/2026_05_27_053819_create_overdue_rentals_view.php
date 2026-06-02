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
        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver");

        if ($driver === 'mongodb') {
            // MongoDB doesn't use SQL views in this way.
            return;
        }

        DB::statement("DROP VIEW IF EXISTS overdue_rentals");

        $readerNameSql = ($driver === 'sqlite' || $driver === 'pgsql') 
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
        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver");

        if ($driver === 'mongodb') {
            return;
        }

        DB::statement("DROP VIEW IF EXISTS overdue_rentals");
    }
};
