<?php

use App\MonitorChecker;
use Illuminate\Database\Seeder;

class MonitorCheckerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MonitorChecker::insert([
            'id' => 'default',
            'action' => 'idle',
        ]);
    }
}
