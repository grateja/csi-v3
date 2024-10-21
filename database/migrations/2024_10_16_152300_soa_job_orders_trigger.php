<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SoaJobOrdersTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
        CREATE TRIGGER update_soajo_totals_after_linen_insert
        AFTER INSERT ON out_source_job_orders
        FOR EACH ROW
        BEGIN
            UPDATE out_source_statement_of_accounts
            SET total_amount = COALESCE((
                SELECT SUM(total_amount)
                FROM out_source_job_orders
                WHERE out_source_statement_of_account_id = NEW.out_source_statement_of_account_id AND out_source_job_orders.deleted_at IS NULL
            ), 0)
            WHERE id = NEW.out_source_statement_of_account_id;
        END;
        ");

        DB::unprepared("
        CREATE TRIGGER update_soajo_totals_after_linen_update
        AFTER UPDATE ON out_source_job_orders
        FOR EACH ROW
        BEGIN
            UPDATE out_source_statement_of_accounts
            SET total_amount = COALESCE((
                SELECT SUM(total_amount)
                FROM out_source_job_orders
                WHERE out_source_statement_of_account_id = NEW.out_source_statement_of_account_id AND out_source_job_orders.deleted_at IS NULL
            ), 0)
            WHERE id = NEW.out_source_statement_of_account_id;
        END;
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS update_soajo_totals_after_linen_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS update_soajo_totals_after_linen_update');
    }
}
