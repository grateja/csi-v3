<?php

use App\JobOrderFormat;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class JobOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(JobOrderFormat::count() == 0) {
            JobOrderFormat::create([
                'id' => 'jo-format-default',
            ]);
        }
    }
}
