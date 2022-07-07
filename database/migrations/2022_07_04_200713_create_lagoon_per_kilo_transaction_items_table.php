<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLagoonPerKiloTransactionItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lagoon_per_kilo_transaction_items', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('transaction_id')->nullable();
            $table->uuid('lagoon_per_kilo_id')->nullable();
            $table->string('name')->nullable();
            $table->double('kilos')->nullable()->default(0);
            $table->double('price_per_kilo')->nullable()->default(0);
            $table->double('total_price')->nullable()->default(0);
            $table->boolean('saved')->default(false);

            $table->timestamps();
            $table->softDeletes();
            $table->timestamp('synched')->nullable();

            $table->foreign('lagoon_per_kilo_id')->references('id')->on('lagoon_per_kilos')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('CASCADE')->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lagoon_per_kilo_transaction_items');
    }
}
