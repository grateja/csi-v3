<?php

use App\StoreHours;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class StoreHoursSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $storeHours = [
            [
                'id' => Str::uuid(),
                'day_index' => 1,
                'opens_at' => '9:00 AM',
                'closes_at' => '7:00 PM',
            ],
            [
                'id' => Str::uuid(),
                'day_index' => 2,
                'opens_at' => '9:00 AM',
                'closes_at' => '7:00 PM',
            ],
            [
                'id' => Str::uuid(),
                'day_index' => 3,
                'opens_at' => '9:00 AM',
                'closes_at' => '7:00 PM',
            ],
            [
                'id' => Str::uuid(),
                'day_index' => 4,
                'opens_at' => '9:00 AM',
                'closes_at' => '7:00 PM',
            ],
            [
                'id' => Str::uuid(),
                'day_index' => 5,
                'opens_at' => '9:00 AM',
                'closes_at' => '7:00 PM',
            ],
            [
                'id' => Str::uuid(),
                'day_index' => 6,
                'opens_at' => '8:00 AM',
                'closes_at' => '8:00 PM',
            ],
            [
                'id' => Str::uuid(),
                'day_index' => 7,
                'opens_at' => '8:00 AM',
                'closes_at' => '8:00 PM',
            ],
        ];

        $storeHours = StoreHours::insert($storeHours);
    }
}
