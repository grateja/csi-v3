<?php

use App\Machine;
use App\EluxMachine;
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
        if(Machine::count() == 0) {
            DB::transaction(function () {
                $machines = [];
                $luxMachines = [];
                for($a = 1; $a <= 2; $a++) {
                    $eluxMachines[] = [
                        'id' => 'lms-demo-ew-' . $a,
                        'stack_order' => $a,
                        'ip_address' => '192.168.210.' . ($a + 10),
                        'machine_type' => 'washer',
                        'model' => 'W130H',
                        'machine_name' => 'Elux Washer ' . $a,
                    ];
                    $eluxMachines[] = [
                        'id' => 'lms-demo-ed-' . $a,
                        'stack_order' => $a,
                        'ip_address' => '192.168.210.' . ($a + 10),
                        'machine_type' => 'dryer',
                        'model' => 'TD130',
                        'machine_name' => 'Elux dryer ' . $a,
                    ];
                }

                EluxMachine::insert($eluxMachines);

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
        }
        return;
    }
}
