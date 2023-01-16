<?php

use App\DryingService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DryingServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(DryingService::count() == 0) {
            $dryingServices = [
                [
                    'id' => Str::uuid(),
                    'name' => 'Regular Dry',
                    'description' => '40 Mins Regular Dry',
                    'price' => 60,
                    'machine_type' => 'REGULAR',
                    'minutes' => 40,
                    'points' => 1,
                ],
                [
                    'id' => Str::uuid(),
                    'name' => 'Titan Dry',
                    'description' => '40 Mins Titan Dry',
                    'price' => 100,
                    'machine_type' => 'TITAN',
                    'minutes' => 40,
                    'points' => 1,
                ],
            ];
    
            DryingService::insert($dryingServices);
        }
    }
}
