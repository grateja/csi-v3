<?php

use App\OtherService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OtherServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(OtherService::count() == 0) {
            $otherServices = [
                [
                    'id' => Str::uuid(),
                    'name' => 'Fold',
                    'description' => 'P30.00 Fold per load',
                    'price' => 30,
                ],
            ];
    
            OtherService::insert($otherServices);
        }
    }
}
