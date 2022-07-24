<?php

use App\Customer;
use App\RfidCard;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RfidCardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $customers = Customer::inRandomOrder()->limit(20)->get();
        foreach ($customers as $customer) {
            $rfid = RfidCard::create([
                'rfid' => Str::random(6),
                'customer_id' => $customer->id,
                'card_type' => 'c',
            ]);
        }
    }
}
