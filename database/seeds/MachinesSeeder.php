<?php

use App\Machine;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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
        DB::transaction(function () {
            $machines = [];
            for($i = 1; $i <= 10; $i++) {
                $machines[] = [
                    'id' => 'lms-demo-rw-' . $i,
                    'stack_order' => $i,
                    'ip_address' => '192.168.210.' . ($i + 10),
                    'machine_type' => 'rw',
                    'machine_name' => 'Washer ' . $i,
                    'initial_time' => 39,
                    'additional_time' => 12,
                    'initial_price' => 60,
                    'additional_price' => 20,
                ];
                $machines[] = [
                    'id' => 'lms-demo-rd-' . $i,
                    'stack_order' => $i,
                    'ip_address' => '192.168.210.' . ($i + 20),
                    'machine_type' => 'rd',
                    'machine_name' => 'Dryer ' . $i,
                    'initial_time' => 10,
                    'additional_time' => 10,
                    'initial_price' => 15,
                    'additional_price' => 15,
                ];
                $machines[] = [
                    'id' => 'lms-demo-tw-' . $i,
                    'stack_order' => $i,
                    'ip_address' => '192.168.210.' . ($i + 30),
                    'machine_type' => 'tw',
                    'machine_name' => 'Titan Washer ' . $i,
                    'initial_time' => 39,
                    'additional_time' => 12,
                    'initial_price' => 80,
                    'additional_price' => 40,
                ];
                $machines[] = [
                    'id' => 'lms-demo-td-' . $i,
                    'stack_order' => $i,
                    'ip_address' => '192.168.210.' . ($i + 40),
                    'machine_type' => 'td',
                    'machine_name' => 'Titan Dryer ' . $i,
                    'initial_time' => 10,
                    'additional_time' => 10,
                    'initial_price' => 40,
                    'additional_price' => 40,
                ];
            }
            Machine::insert($machines);
        });
        return;

        $machines = [
            [
                'id' => 'lms-demo-rw-1',
                'ip_address' => '192.168.210.11',
                'machine_type' => 'rw',
                'machine_name' => 'Washer 1',
                'initial_time' => 39,
                'additional_time' => 12,
                'initial_price' => 60,
                'additional_price' => 20,
            ],
            [
                'id' => 'lms-demo-rw-2',
                'ip_address' => '192.168.210.12',
                'machine_type' => 'rw',
                'machine_name' => 'Washer 2',
                'initial_time' => 39,
                'additional_time' => 12,
                'initial_price' => 60,
                'additional_price' => 20,
            ],
            [
                'id' => 'lms-demo-rw-3',
                'ip_address' => '192.168.210.13',
                'machine_type' => 'rw',
                'machine_name' => 'Washer 3',
                'initial_time' => 39,
                'additional_time' => 12,
                'initial_price' => 60,
                'additional_price' => 20,
            ],
            [
                'id' => 'lms-demo-rw-4',
                'ip_address' => '192.168.210.14',
                'machine_type' => 'rw',
                'machine_name' => 'Washer 4',
                'initial_time' => 39,
                'additional_time' => 12,
                'initial_price' => 60,
                'additional_price' => 20,
            ],
            [
                'id' => 'lms-demo-rw-5',
                'ip_address' => '192.168.210.15',
                'machine_type' => 'rw',
                'machine_name' => 'Washer 5',
                'initial_time' => 39,
                'additional_time' => 12,
                'initial_price' => 60,
                'additional_price' => 20,
            ],
            [
                'id' => 'lms-demo-rd-1',
                'ip_address' => '192.168.210.31',
                'machine_type' => 'rd',
                'machine_name' => 'Dryer 1',
                'initial_time' => 10,
                'additional_time' => 10,
                'initial_price' => 15,
                'additional_price' => 15,
            ],
            [
                'id' => 'lms-demo-rd-2',
                'ip_address' => '192.168.210.32',
                'machine_type' => 'rd',
                'machine_name' => 'Dryer 2',
                'initial_time' => 10,
                'additional_time' => 10,
                'initial_price' => 15,
                'additional_price' => 15,
            ],
            [
                'id' => 'lms-demo-rd-3',
                'ip_address' => '192.168.210.33',
                'machine_type' => 'rd',
                'machine_name' => 'Dryer 3',
                'initial_time' => 10,
                'additional_time' => 10,
                'initial_price' => 15,
                'additional_price' => 15,
            ],
            [
                'id' => 'lms-demo-rd-4',
                'ip_address' => '192.168.210.34',
                'machine_type' => 'rd',
                'machine_name' => 'Dryer 4',
                'initial_time' => 10,
                'additional_time' => 10,
                'initial_price' => 15,
                'additional_price' => 15,
            ],
            [
                'id' => 'lms-demo-rd-5',
                'ip_address' => '192.168.210.35',
                'machine_type' => 'rd',
                'machine_name' => 'Dryer 5',
                'initial_time' => 10,
                'additional_time' => 10,
                'initial_price' => 15,
                'additional_price' => 15,
            ],
            [
                'id' => 'lms-demo-tw-1',
                'ip_address' => '192.168.210.21',
                'machine_type' => 'tw',
                'machine_name' => 'Titan Washer 1',
                'initial_time' => 39,
                'additional_time' => 12,
                'initial_price' => 90,
                'additional_price' => 30,
            ],
            [
                'id' => 'lms-demo-tw-2',
                'ip_address' => '192.168.210.22',
                'machine_type' => 'tw',
                'machine_name' => 'Titan Washer 2',
                'initial_time' => 39,
                'additional_time' => 12,
                'initial_price' => 90,
                'additional_price' => 30,
            ],
            [
                'id' => 'lms-demo-tw-3',
                'ip_address' => '192.168.210.23',
                'machine_type' => 'tw',
                'machine_name' => 'Titan Washer 3',
                'initial_time' => 39,
                'additional_time' => 12,
                'initial_price' => 90,
                'additional_price' => 30,
            ],
            [
                'id' => 'lms-demo-tw-4',
                'ip_address' => '192.168.210.24',
                'machine_type' => 'tw',
                'machine_name' => 'Titan Washer 4',
                'initial_time' => 39,
                'additional_time' => 12,
                'initial_price' => 90,
                'additional_price' => 30,
            ],
            [
                'id' => 'lms-demo-tw-5',
                'ip_address' => '192.168.210.25',
                'machine_type' => 'tw',
                'machine_name' => 'Titan Washer 5',
                'initial_time' => 39,
                'additional_time' => 12,
                'initial_price' => 90,
                'additional_price' => 30,
            ],
            [
                'id' => 'lms-demo-td-1',
                'ip_address' => '192.168.210.41',
                'machine_type' => 'td',
                'machine_name' => 'Titan Dryer 1',
                'initial_time' => 10,
                'additional_time' => 10,
                'initial_price' => 30,
                'additional_price' => 30,
            ],
            [
                'id' => 'lms-demo-td-2',
                'ip_address' => '192.168.210.42',
                'machine_type' => 'td',
                'machine_name' => 'Titan Dryer 2',
                'initial_time' => 10,
                'additional_time' => 10,
                'initial_price' => 30,
                'additional_price' => 30,
            ],
            [
                'id' => 'lms-demo-td-3',
                'ip_address' => '192.168.210.43',
                'machine_type' => 'td',
                'machine_name' => 'Titan Dryer 3',
                'initial_time' => 10,
                'additional_time' => 10,
                'initial_price' => 30,
                'additional_price' => 30,
            ],
            [
                'id' => 'lms-demo-td-4',
                'ip_address' => '192.168.210.44',
                'machine_type' => 'td',
                'machine_name' => 'Titan Dryer 4',
                'initial_time' => 10,
                'additional_time' => 10,
                'initial_price' => 30,
                'additional_price' => 30,
            ],
            [
                'id' => 'lms-demo-td-5',
                'ip_address' => '192.168.210.45',
                'machine_type' => 'td',
                'machine_name' => 'Titan Dryer 5',
                'initial_time' => 10,
                'additional_time' => 10,
                'initial_price' => 30,
                'additional_price' => 30,
            ],
        ];
        Machine::insert($machines);
    }
}
