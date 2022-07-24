<?php

use Illuminate\Database\Seeder;

class EventTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $eventTypes = [
            [
                'id' => 1,
                'name' => 'slides',
            ],
            [
                'id' => 2,
                'name' => 'video',
            ],
            [
                'id' => 3,
                'name' => 'text',
            ],
            [
                'id' => 4,
                'name' => 'youtube',
            ],
        ];

        \App\EventType::insert($eventTypes);
    }
}
