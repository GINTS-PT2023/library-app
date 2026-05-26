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
        Schema::create('book_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('book_id');
            $table->string('column_name');
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->timestamp('changed_at')->useCurrent();
        });

        // SQLite trigger to log changes to available_copies and total_copies
        DB::unprepared('
            CREATE TRIGGER log_book_copies_update
            AFTER UPDATE OF available_copies, total_copies ON books
            BEGIN
                INSERT INTO book_logs (book_id, column_name, old_value, new_value)
                SELECT 
                    OLD.id, 
                    \'available_copies\', 
                    OLD.available_copies, 
                    NEW.available_copies
                WHERE OLD.available_copies <> NEW.available_copies;

                INSERT INTO book_logs (book_id, column_name, old_value, new_value)
                SELECT 
                    OLD.id, 
                    \'total_copies\', 
                    OLD.total_copies, 
                    NEW.total_copies
                WHERE OLD.total_copies <> NEW.total_copies;
            END;
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS log_book_copies_update');
        Schema::dropIfExists('book_logs');
    }
};
