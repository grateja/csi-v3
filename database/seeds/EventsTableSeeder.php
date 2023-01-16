<?php

use App\Event;
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
        if(!DB::table('events')->where('id', 'event-default')->exists()) {
            Event::create([
                'id' => 'event-default',
                'date_from' => \Carbon\Carbon::now(),
                'title' => 'Start up event',
                'description' => 'Something to say',
                'published' => true,
                'event_type_id' => 1
            ]);
        }
    }
}
