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

        if ($driver === 'sqlite') {
            // SQLite doesn't support stored procedures, so we use a VIEW to centralize the logic.
            // This view calculates the total fine for each reader based on overdue rentals.
            DB::statement("DROP VIEW IF EXISTS reader_fines");
            DB::statement("
                CREATE VIEW reader_fines AS
                SELECT 
                    reader_id,
                    SUM(CASE 
                        WHEN julianday(date(COALESCE(returned_at, CURRENT_TIMESTAMP))) > julianday(date(due_at)) 
                        THEN (julianday(date(COALESCE(returned_at, CURRENT_TIMESTAMP))) - julianday(date(due_at))) * 0.50
                        ELSE 0 
                    END) AS total_fine
                FROM rentals
                GROUP BY reader_id
            ");
        } else {
            // For MySQL/MariaDB/PostgreSQL, we could create a real stored procedure.
            // This is a placeholder example for MySQL.
            DB::unprepared("DROP PROCEDURE IF EXISTS calculate_reader_fine");
            DB::unprepared("
                CREATE PROCEDURE calculate_reader_fine(IN r_id INT, OUT total_fine DECIMAL(10,2))
                BEGIN
                    SELECT SUM(IF(DATEDIFF(COALESCE(returned_at, NOW()), due_at) > 0, 
                                  DATEDIFF(COALESCE(returned_at, NOW()), due_at) * 0.50, 0))
                    INTO total_fine
                    FROM rentals
                    WHERE reader_id = r_id;
                END
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver");

        if ($driver === 'sqlite') {
            DB::statement("DROP VIEW IF EXISTS reader_fines");
        } else {
            DB::unprepared("DROP PROCEDURE IF EXISTS calculate_reader_fine");
        }
    }
};
