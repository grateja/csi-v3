<?php

use App\WashingService;
use App\EluxService;
use App\OutSourceService;
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

            $eluxServices = [
                [
                    'id' => Str::uuid(),
                    'service_type' => 'washer',
                    'name' => 'Cold Wash',
                    'price' => 70,
                    'pulse_count' => 1,
                    'model' => 'W130H',
                    'minutes' => 40,
                ],
                [
                    'id' => Str::uuid(),
                    'service_type' => 'washer',
                    'name' => 'Warm Wash',
                    'price' => 80,
                    'pulse_count' => 1,
                    'model' => 'W130H',
                    'minutes' => 45,
                ],
                [
                    'id' => Str::uuid(),
                    'service_type' => 'washer',
                    'name' => 'Hot Wash',
                    'price' => 80,
                    'pulse_count' => 1,
                    'model' => 'W130H',
                    'minutes' => 50,
                ],
                [
                    'id' => Str::uuid(),
                    'service_type' => 'dryer',
                    'name' => 'Regular dry',
                    'price' => 80,
                    'pulse_count' => 1,
                    'model' => 'TD130',
                    'minutes' => 40,
                ],
            ];
            EluxService::insert($eluxServices);

            $outSourceServices = [
                [
                    'id' => Str::uuid(),
                    'name' => 'Cold wash',
                    'description' => '20C cold wash',
                    'pulse_count' => 1,
                    'minutes' => 40,
                ],
                [
                    'id' => Str::uuid(),
                    'name' => 'Warm wash',
                    'description' => '40C warm wash',
                    'pulse_count' => 2,
                    'minutes' => 45,
                ],
                [
                    'id' => Str::uuid(),
                    'name' => 'Hot wash',
                    'description' => '60C hot wash',
                    'pulse_count' => 3,
                    'minutes' => 50,
                ],
            ];

            OutSourceService::insert($outSourceServices);
        }
    }
}
