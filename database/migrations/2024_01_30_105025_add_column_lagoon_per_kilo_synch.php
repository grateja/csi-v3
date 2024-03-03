<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnLagoonPerKiloSynch extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lagoon_per_kilos', function (Blueprint $table) {
            if (!Schema::hasColumn('lagoon_per_kilos', 'synched')) {
                $table->timestamp('synched')->after('deleted_at')->default(null)->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
