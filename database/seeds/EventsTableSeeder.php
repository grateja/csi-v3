<?php

use Illuminate\Database\Seeder;

class EventsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $events = [
            [
                'id' => 'event-default',
                'date' => \Carbon\Carbon::now(),
                'title' => 'Start up event',
                'description' => 'Something to say',
                'published' => true,
                'event_type_id' => 1
            ]
        ];

        \App\Event::insert($events);
    }
}
