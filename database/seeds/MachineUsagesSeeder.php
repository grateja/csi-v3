<?php

use App\Customer;
use App\CustomerDry;
use App\CustomerWash;
use App\Machine;
use App\MachineUsage;
use App\RfidCard;
use App\RfidCardTransaction;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class MachineUsagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::transaction(function () {
            for ($i=0; $i < 5000; $i++) {
                $minutes = 0;
                $price = 0;

                $date = Carbon::now()->subDay(rand(0, 90));

                $machine = Machine::inRandomOrder()->first();
                $rfidCard = RfidCard::inRandomOrder()->first();
                $customer = $rfidCard->customer;

                if($machine->machine_type[1] == 'w') {
                    // this is washer
                    if(rand(0, 2)) {
                        // regular wash
                        $minutes = $machine->initial_time;
                        $price = $machine->initial_price;
                    } else {
                        // add superwash
                        $minutes = $machine->initial_time + $machine->additional_time;
                        $price = $machine->initial_price + $machine->additional_price;
                    }
                } else {
                    // this is dryer
                    if(rand(0, 2)) {
                        // regular dry
                        $minutes = $machine->initial_time * 4;
                        $price = $machine->initial_price * 4;
                    } else if(rand(0, 2)) {
                        // additional dry only
                        $minutes = $machine->initial_time;
                        $price = $machine->initial_price;
                    } else {
                        // regular dry with additional dry
                        $minutes = $machine->initial_time * 4 + $machine->additional_time;
                        $price = $machine->initial_price * 4 + $machine->additional_price;
                    }
                }

                MachineUsage::create([
                    'machine_id' => $machine->id,
                    'customer_name' => $rfidCard->owner_name,
                    'minutes' => $minutes,
                    'activation_type' => 'card',
                    'price' => $price,
                    'created_at' => $date,
                ]);

                RfidCardTransaction::create([
                    'rfid' => $rfidCard->rfid,
                    'machine_name' => $machine->machine_name,
                    'owner_name' => $rfidCard->owner_name,
                    'price' => $price,
                    'card_type' => $rfidCard->card_type,
                    'minutes' => $minutes,
                    'machine_id' => $machine->id,
                    'rfid_card_id' => $rfidCard->id,
                    'created_at' => $date,
                ]);
            }

            $customerDries = CustomerDry::whereNull('used')->get();
            foreach ($customerDries as $customerDry) {
                $machineType = strtolower($customerDry->machine_type[0]);
                $machine = Machine::where('machine_type', $machineType . 'd')->inRandomOrder()->first();
                $date = $customerDry->created_at->subMinutes(rand(10, 10000));
                $customer = $customerDry->customer;

                MachineUsage::create([
                    'machine_id' => $machine->id,
                    'customer_name' => $customer->name,
                    'minutes' => $customerDry->minutes,
                    'activation_type' => 'remote',
                    'price' => $customerDry->price,
                    'created_at' => $date,
                ]);

                $customerDry->update([
                    'used' => $date,
                    'staff_name' => str_random(10),
                ]);
            }

            $customerWashes = CustomerWash::whereNull('used')->get();
            foreach ($customerWashes as $customerWash) {
                $machineType = strtolower($customerWash->machine_type[0]);
                $machine = Machine::where('machine_type', $machineType . 'w')->inRandomOrder()->first();
                $date = $customerWash->created_at->subMinutes(rand(10, 10000));
                $customer = $customerWash->customer;

                MachineUsage::create([
                    'machine_id' => $machine->id,
                    'customer_name' => $customer->name,
                    'minutes' => $customerWash->minutes,
                    'activation_type' => 'remote',
                    'price' => $customerWash->price,
                    'created_at' => $date,
                ]);

                $customerWash->update([
                    'used' => $date,
                    'staff_name' => str_random(10),
                ]);
            }
        });
    }
}
