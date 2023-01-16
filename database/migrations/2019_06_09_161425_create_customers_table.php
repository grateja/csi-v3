<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('customers')) {
            Schema::create('customers', function (Blueprint $table) {
                $table->uuid('id')->primary();

                $table->string('crn')->comment('customer reference number')->nullable()->default('0000');
                $table->string('name')->nullable();
                $table->string('address')->nullable();
                $table->string('contact_number')->nullable();
                $table->string('email')->nullable();
                $table->double('earned_points')->default(0)->nullable();
                $table->date('first_visit')->nullable();
                $table->text('remarks')->nullable();

                $table->softDeletes();
                $table->timestamps();

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
        Schema::dropIfExists('customers');
    }
}
