<?php

use App\LoyaltyPoint;
use Illuminate\Database\Seeder;

class PointsDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        LoyaltyPoint::create([
            'amount_in_peso' => 10,
        ]);
    }
}
