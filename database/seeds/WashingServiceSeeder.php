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
        if(WashingService::count() == 0) {
            $washingServices = [
                [
                    'id' => Str::uuid(),
                    'name' => 'Regular Wash',
                    'description' => '38 Mins Regular Wash',
                    'price' => 60,
                    'machine_type' => 'REGULAR',
                    'regular_minutes' => 38,
                    'quick_minutes' => 0,
                    'more_rinse_minutes' => 0,
                    'premium_minutes' => 0,
                    'additional_minutes' => 0,
                    'points' => 1,
                ],
                [
                    'id' => Str::uuid(),
                    'name' => 'Quick Wash',
                    'description' => '24 Mins Wash',
                    'price' => 50,
                    'machine_type' => 'REGULAR',
                    'regular_minutes' => 0,
                    'quick_minutes' => 24,
                    'more_rinse_minutes' => 0,
                    'premium_minutes' => 0,
                    'additional_minutes' => 0,
                    'points' => 1,
                ],
                [
                    'id' => Str::uuid(),
                    'name' => 'Premium Wash',
                    'description' => '46 Mins Dry',
                    'price' => 100,
                    'machine_type' => 'REGULAR',
                    'regular_minutes' => 0,
                    'quick_minutes' => 0,
                    'more_rinse_minutes' => 0,
                    'premium_minutes' => 46,
                    'additional_minutes' => 0,
                    'points' => 1,
                ],
                [
                    'id' => Str::uuid(),
                    'name' => 'Super Wash',
                    'description' => '52 Mins Dry',
                    'price' => 100,
                    'machine_type' => 'REGULAR',
                    'regular_minutes' => 0,
                    'quick_minutes' => 0,
                    'more_rinse_minutes' => 0,
                    'premium_minutes' => 0,
                    'additional_minutes' => 52,
                    'points' => 1,
                ],
            ];
    
            WashingService::insert($washingServices);
        }
    }
}
