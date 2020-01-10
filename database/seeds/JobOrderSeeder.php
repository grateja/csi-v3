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
        JobOrderFormat::create([
            'id' => Str::uuid(),
        ]);
    }
}
