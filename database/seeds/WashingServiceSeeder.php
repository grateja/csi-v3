<?php

use App\WashingService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class WashingServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $washingServices = [
            [
                'id' => Str::uuid(),
                'name' => 'Regular Wash',
                'description' => '38 Mins Regular Dry',
                'price' => 60,
                'machine_type' => 'REGULAR',
                'regular_minutes' => 38,
                'additional_minutes' => 0,
                'points' => 1,
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Titan Wash',
                'description' => '38 Mins Titan Dry',
                'price' => 100,
                'machine_type' => 'TITAN',
                'regular_minutes' => 38,
                'additional_minutes' => 0,
                'points' => 1,
            ],
        ];

        WashingService::insert($washingServices);
    }
}
