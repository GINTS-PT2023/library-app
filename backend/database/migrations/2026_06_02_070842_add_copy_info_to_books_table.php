<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop view if exists to avoid SQLite rename issues
        DB::statement("DROP VIEW IF EXISTS overdue_rentals");

        Schema::table('books', function (Blueprint $table) {
            $table->foreignId('copied_from_id')->nullable()->constrained('books')->nullOnDelete();
            $table->timestamp('copied_at')->nullable();
        });

        // Recreate the view
        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver");
        
        if ($driver !== 'mongodb') {
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropForeign(['copied_from_id']);
            $table->dropColumn(['copied_from_id', 'copied_at']);
        });
    }
};
