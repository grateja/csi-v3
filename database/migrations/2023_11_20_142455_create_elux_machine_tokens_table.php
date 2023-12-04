<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEluxMachineTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('elux_machine_tokens')) {
            Schema::create('elux_machine_tokens', function (Blueprint $table) {
                $table->uuid('id')->primary();

                $table->string('service_type')->nullable()->comment('washer/dryer');
                $table->string('name');
                $table->integer('pulse_count');
                $table->string('lagoon_transaction_item_id');
                $table->timestamp('used');

                $table->timestamps();
                $table->softDeletes();
                $table->timestamp('synched')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('elux_machine_tokens');
    }
}
