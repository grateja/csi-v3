<?php

use App\MonitorChecker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MonitorCheckerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(!DB::table('monitor_checkers')->where('id', 'default')->exists()) {
            MonitorChecker::insert([
                'id' => 'default',
                'action' => 'idle',
            ]);
        }
    }
}
