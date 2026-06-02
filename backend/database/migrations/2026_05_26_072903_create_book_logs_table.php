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

        $driver = DB::getDriverName();

        if ($driver === 'mongodb') {
            return;
        }

        if ($driver === 'sqlite') {
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
        } elseif ($driver === 'pgsql') {
            DB::unprepared("
                CREATE OR REPLACE FUNCTION log_book_changes() RETURNS TRIGGER AS $$
                BEGIN
                    IF OLD.available_copies <> NEW.available_copies THEN
                        INSERT INTO book_logs (book_id, column_name, old_value, new_value)
                        VALUES (OLD.id, 'available_copies', OLD.available_copies, NEW.available_copies);
                    END IF;
                    IF OLD.total_copies <> NEW.total_copies THEN
                        INSERT INTO book_logs (book_id, column_name, old_value, new_value)
                        VALUES (OLD.id, 'total_copies', OLD.total_copies, NEW.total_copies);
                    END IF;
                    RETURN NEW;
                END;
                $$ LANGUAGE plpgsql;

                CREATE TRIGGER log_book_copies_update
                AFTER UPDATE OF available_copies, total_copies ON books
                FOR EACH ROW EXECUTE FUNCTION log_book_changes();
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mongodb') {
            Schema::dropIfExists('book_logs');
            return;
        }
        
        if ($driver === 'sqlite') {
            DB::unprepared('DROP TRIGGER IF EXISTS log_book_copies_update');
        } elseif ($driver === 'pgsql') {
            DB::unprepared('DROP TRIGGER IF EXISTS log_book_copies_update ON books');
            DB::unprepared('DROP FUNCTION IF EXISTS log_book_changes()');
        }
        
        Schema::dropIfExists('book_logs');
    }
};
