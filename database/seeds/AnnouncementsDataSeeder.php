<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AnnouncementsDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(!DB::table('announcements')->where('id', 'announcement-default')->exists()) {
            \App\Announcement::create([
                'id' => 'announcement-default',
                'content' => 'https://www.elsphillipines.com Tel. No. : 721-8595 / 721-8996',
                'date_from' => date('Y-m-d'),
                'date_until' => date('Y-m-d'),
            ]);
        }
    }
}
