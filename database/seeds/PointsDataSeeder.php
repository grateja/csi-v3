<?php

use App\LoyaltyPoint;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PointsDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(LoyaltyPoint::count() == 0) {
            LoyaltyPoint::create([
                'amount_in_peso' => 10,
            ]);
        }
    }
}
