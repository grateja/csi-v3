<?php

use Illuminate\Database\Seeder;

class AnnouncementsDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Announcement::create([
            'id' => 'announcement-default',
            'content' => 'https://www.elsphillipines.com Tel. No. : 721-8595 / 721-8996',
            'date' => date('Y-m-d'),
        ]);
    }
}
