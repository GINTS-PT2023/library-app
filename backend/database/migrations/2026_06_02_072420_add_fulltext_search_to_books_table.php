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
        // Full-text search is supported in MySQL and PostgreSQL.
        // For SQLite, we will skip adding the specific index as Laravel's schema builder doesn't support it for SQLite.
        if (DB::getDriverName() !== 'sqlite') {
            Schema::table('books', function (Blueprint $table) {
                $table->fullText(['title', 'author'], 'books_fulltext_search');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            Schema::table('books', function (Blueprint $table) {
                $table->dropFullText('books_fulltext_search');
            });
        }
    }
};
