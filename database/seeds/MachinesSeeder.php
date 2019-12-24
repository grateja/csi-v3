<?php

use App\Machine;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MachinesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $machines = [
            [
                'id' => Str::uuid(),
                'ip_address' => '192.168.210.11',
                'machine_type' => 'rw',
                'machine_name' => 'washer1',
                'initial_time' => 39,
                'additional_time' => 12,
                'initial_price' => 60,
                'additional_price' => 20,
            ],
            [
                'id' => Str::uuid(),
                'ip_address' => '192.168.210.12',
                'machine_type' => 'rw',
                'machine_name' => 'washer2',
                'initial_time' => 39,
                'additional_time' => 12,
                'initial_price' => 60,
                'additional_price' => 20,
            ],
            [
                'id' => Str::uuid(),
                'ip_address' => '192.168.210.13',
                'machine_type' => 'rw',
                'machine_name' => 'washer3',
                'initial_time' => 39,
                'additional_time' => 12,
                'initial_price' => 60,
                'additional_price' => 20,
            ],
            [
                'id' => Str::uuid(),
                'ip_address' => '192.168.210.14',
                'machine_type' => 'rw',
                'machine_name' => 'washer4',
                'initial_time' => 39,
                'additional_time' => 12,
                'initial_price' => 60,
                'additional_price' => 20,
            ],
            [
                'id' => Str::uuid(),
                'ip_address' => '192.168.210.15',
                'machine_type' => 'rw',
                'machine_name' => 'washer5',
                'initial_time' => 39,
                'additional_time' => 12,
                'initial_price' => 60,
                'additional_price' => 20,
            ],
            [
                'id' => Str::uuid(),
                'ip_address' => '192.168.210.31',
                'machine_type' => 'rd',
                'machine_name' => 'dryer1',
                'initial_time' => 10,
                'additional_time' => 10,
                'initial_price' => 15,
                'additional_price' => 15,
            ],
            [
                'id' => Str::uuid(),
                'ip_address' => '192.168.210.32',
                'machine_type' => 'rd',
                'machine_name' => 'dryer2',
                'initial_time' => 10,
                'additional_time' => 10,
                'initial_price' => 15,
                'additional_price' => 15,
            ],
            [
                'id' => Str::uuid(),
                'ip_address' => '192.168.210.33',
                'machine_type' => 'rd',
                'machine_name' => 'dryer3',
                'initial_time' => 10,
                'additional_time' => 10,
                'initial_price' => 15,
                'additional_price' => 15,
            ],
            [
                'id' => Str::uuid(),
                'ip_address' => '192.168.210.34',
                'machine_type' => 'rd',
                'machine_name' => 'dryer4',
                'initial_time' => 10,
                'additional_time' => 10,
                'initial_price' => 15,
                'additional_price' => 15,
            ],
            [
                'id' => Str::uuid(),
                'ip_address' => '192.168.210.35',
                'machine_type' => 'rd',
                'machine_name' => 'dryer5',
                'initial_time' => 10,
                'additional_time' => 10,
                'initial_price' => 15,
                'additional_price' => 15,
            ],
        ];
        Machine::insert($machines);
    }
}
