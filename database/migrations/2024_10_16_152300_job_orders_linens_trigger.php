<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class JobOrdersLinensTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
        CREATE TRIGGER update_job_orders_totals_after_linen_insert
        AFTER INSERT ON out_source_job_order_linens
        FOR EACH ROW
        BEGIN
            UPDATE out_source_job_orders
            SET total_amount = COALESCE((
                SELECT SUM(unit_price * quantity)
                FROM out_source_job_order_linens
                WHERE out_source_job_order_id = NEW.out_source_job_order_id AND out_source_job_order_linens.deleted_at IS NULL
            ), 0)
            WHERE id = NEW.out_source_job_order_id;
        END;
        ");

        DB::unprepared("
        CREATE TRIGGER update_job_orders_totals_after_linen_update
        AFTER UPDATE ON out_source_job_order_linens
        FOR EACH ROW
        BEGIN
            UPDATE out_source_job_orders
            SET total_amount = COALESCE((
                SELECT SUM(unit_price * quantity)
                FROM out_source_job_order_linens
                WHERE out_source_job_order_id = NEW.out_source_job_order_id AND out_source_job_order_linens.deleted_at IS NULL
            ), 0)
            WHERE id = NEW.out_source_job_order_id;
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
        DB::unprepared('DROP TRIGGER IF EXISTS update_job_orders_totals_after_linen_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS update_job_orders_totals_after_linen_update');
    }
}
