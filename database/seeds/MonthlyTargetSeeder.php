<?php

use App\MonthlyTarget;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MonthlyTargetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $monthlyTargets = [];
        for ($i=0; $i < 12; $i++) {
            $monthlyTargets[] = ['index' => $i, 'id' => Str::uuid()];
        }
        MonthlyTarget::insert($monthlyTargets);
    }
}
