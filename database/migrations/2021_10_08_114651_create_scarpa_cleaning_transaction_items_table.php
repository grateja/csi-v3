<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScarpaCleaningTransactionItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scarpa_cleaning_transaction_items', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('transaction_id');

            $table->uuid('scarpa_category_id')->nullable();
            $table->uuid('scarpa_variation_id')->nullable();
            $table->string('name');
            $table->double('price')->default(0);
            $table->boolean('saved')->default(0);

            $table->softDeletes();
            $table->timestamps();
            $table->timestamp('synched')->nullable();

            $table->foreign('scarpa_category_id')->references('id')->on('scarpa_categories')->onUpdate('CASCADE')->onDelete('SET NULL');
            $table->foreign('scarpa_variation_id')->references('id')->on('scarpa_variations')->onUpdate('CASCADE')->onDelete('SET NULL');
            $table->foreign('transaction_id')->references('id')->on('transactions')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('scarpa_cleaning_transaction_items');
    }
}
