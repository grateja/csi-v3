<?php

use App\Customer;
use App\CustomerDry;
use App\CustomerWash;
use App\DryingService;
use App\FullService;
use App\OtherService;
use App\Product;
use App\ProductTransactionItem;
use App\ServiceTransactionItem;
use App\Transaction;
use App\TransactionPayment;
use App\User;
use App\WashingService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransactionsSeeder extends Seeder
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
                $date = Carbon::now()->subDay(rand(0, 90));
                $customer = Customer::inRandomOrder()->first();
                $user = User::inRandomOrder()->first();

                $total_price = 0;
                $transactionId = Str::uuid();
                $datePaid = $date->subDay(1, 3);

                $transaction = Transaction::create([
                    'id' => $transactionId,
                    'date' => $date,
                    'customer_id' => $customer->id,
                    'customer_name' => $customer->name,
                    'user_id' => $user->id,
                    'staff_name' => $user->name,
                    'job_order' => '#' . Str::random(6),
                    'saved' => 1,
                    'date_paid' => $datePaid,
                    'created_at' => $date,
                ]);

                $products = Product::inRandomOrder()->limit(rand(1, 4))->get();

                foreach ($products as $product) {
                    $productItem = ProductTransactionItem::create([
                        'transaction_id' => $transactionId,
                        'name' => $product->name,
                        'price' => $product->selling_price,
                        'product_id' => $product->id,
                        'saved' => 1,
                        'created_at' => $date,
                    ]);
                    $total_price += $product->selling_price;
                }

                $wServices = WashingService::inRandomOrder()->limit(1, 2)->get();
                foreach ($wServices as $wService) {
                    $serviceItem = ServiceTransactionItem::create([
                        'transaction_id' => $transactionId,
                        'name' => $wService->name,
                        'price' => $wService->price,
                        'category' => 'washing',
                        'washing_service_id' => $wService->id,
                        'saved' => 1,
                        'created_at' => $date,
                    ]);
                    $customerWash = CustomerWash::create([
                        'customer_id' => $customer->id,
                        'service_transaction_item_id' => $serviceItem->id,
                        'service_name' => $wService->name,
                        'machine_type' => $wService->machine_type,
                        'pulse_count' => $wService->additional_minutes ? 2 : 1,
                        'minutes' => $wService->regular_minutes + $wService->additional_minutes,
                        'price' => $wService->price,
                        'created_at' => $date,
                    ]);
                    $total_price += $wService->price;
                }

                $dServices = DryingService::inRandomOrder()->limit(1, 2)->get();
                foreach ($dServices as $dService) {
                    $serviceItem = ServiceTransactionItem::create([
                        'transaction_id' => $transactionId,
                        'name' => $dService->name,
                        'price' => $dService->price,
                        'category' => 'drying',
                        'drying_service_id' => $dService->id,
                        'saved' => 1,
                        'created_at' => $date,
                    ]);
                    $customerWash = CustomerDry::create([
                        'customer_id' => $customer->id,
                        'service_transaction_item_id' => $serviceItem->id,
                        'service_name' => $wService->name,
                        'machine_type' => $wService->machine_type,
                        'pulse_count' => $wService->minutes / 10,
                        'minutes' => $wService->regular_minutes + $wService->additional_minutes,
                        'price' => $wService->price,
                        'created_at' => $date,
                    ]);
                    $total_price += $dService->price;
                }

                $oServices = OtherService::inRandomOrder()->limit(1, 2)->get();
                foreach ($oServices as $oService) {
                    $serviceItem = ServiceTransactionItem::create([
                        'transaction_id' => $transactionId,
                        'name' => $oService->name,
                        'price' => $oService->price,
                        'category' => 'other',
                        'other_service_id' => $oService->id,
                        'saved' => 1,
                        'created_at' => $date,
                    ]);
                    $total_price += $oService->price;
                }

                $fServices = FullService::inRandomOrder()->limit(1, 2)->get();
                foreach ($fServices as $fService) {
                    $serviceItem = ServiceTransactionItem::create([
                        'transaction_id' => $transactionId,
                        'name' => $fService->name,
                        'price' => $fService->price,
                        'category' => 'full',
                        'full_service_id' => $fService->id,
                        'saved' => 1,
                        'created_at' => $date,
                    ]);
                    $total_price += $fService->price;
                }

                $transaction->update([
                    'total_price' => $total_price,
                ]);

                $payment = TransactionPayment::create([
                    'id' => $transactionId,
                    'date' => $datePaid,
                    'cash' => $total_price,
                    'total_amount' => $total_price,
                    'paid_to' => $user->name,
                    'user_id' => $user->id,
                    'created_at' => $date,
                ]);
            }
        });
    }
}
