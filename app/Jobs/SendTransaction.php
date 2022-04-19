<?php

namespace App\Jobs;

use App\Client;
use App\Customer;
use App\LagoonTransactionItem;
use App\PartialPayment;
use App\ProductTransactionItem;
use App\ScarpaCleaningTransactionItem;
use App\ServiceTransactionItem;
use App\Transaction;
use App\TransactionPayment;
use App\TransactionRemarks;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use GuzzleHttp\Client as GuzzleHttpClient;
use Illuminate\Support\Facades\DB;

class SendTransaction implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $transactionId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($transactionId)
    {
        $this->transactionId = $transactionId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $shopId = Client::first()->user_id;

        $transaction = Transaction::withTrashed()->find($this->transactionId);
        $customer = Customer::withTrashed()->find($transaction->customer_id);

        $data = [
            'transaction' => $transaction,
            'customer' => $customer,
            'service_transaction_items' => ServiceTransactionItem::withTrashed()->where('transaction_id', $transaction->id)->get(),
            'product_transaction_items' => ProductTransactionItem::withTrashed()->where('transaction_id', $transaction->id)->get(),
            'lagoon_transaction_items' => LagoonTransactionItem::withTrashed()->where('transaction_id', $transaction->id)->get(),
            'scarpa_cleaning_transaction_items' => ScarpaCleaningTransactionItem::withTrashed()->where('transaction_id', $transaction->id)->get(),
            'payment' => TransactionPayment::withTrashed()->find($transaction->id),
            'partial_payment' => PartialPayment::withTrashed()->find($transaction->id),
            'remarks' => TransactionRemarks::withTrashed()->where('transaction_id', $transaction->id),
        ];

        $response = $this->createRequest($data, $shopId);
        if($response->getStatusCode() == 200) {
            $result = json_decode($response->getBody());

            if($result->transaction_id) {
                DB::table('transactions')->where('id', $result->transaction_id)->update([
                    'synched' => Carbon::now(),
                ]);
            }
            if($result->customer_id) {
                DB::table('customers')->where('id', $result->customer_id)->update([
                    'synched' => Carbon::now(),
                ]);
            }
            if($result->payment_id) {
                DB::table('transaction_payments')->where('id', $result->payment_id)->update([
                    'synched' => Carbon::now(),
                ]);
            }
            if($result->service_transaction_item_ids) {
                DB::table('service_transaction_items')->whereIn('id', $result->service_transaction_item_ids)->update([
                    'synched' => Carbon::now(),
                ]);
            }
            if($result->product_transaction_item_ids) {
                DB::table('product_transaction_items')->whereIn('id', $result->product_transaction_item_ids)->update([
                    'synched' => Carbon::now(),
                ]);
            }
            if($result->scarpa_cleaning_transaction_item_ids) {
                DB::table('scarpa_cleaning_transaction_items')->whereIn('id', $result->scarpa_cleaning_transaction_item_ids)->update([
                    'synched' => Carbon::now(),
                ]);
            }
            if($result->lagoon_transaction_item_ids) {
                DB::table('lagoon_transaction_items')->whereIn('id', $result->lagoon_transaction_item_ids)->update([
                    'synched' => Carbon::now(),
                ]);
            }
        }
    }

    private function createRequest($data, $shopId) {
        $clientRequest = new GuzzleHttpClient();
        $response = $clientRequest->post('http://139.162.73.87/api/live/v3/update/' . $shopId . '/transaction', [
        // $response = $clientRequest->post('http://csi-v3-live/api/live/v3/update/' . $shopId . '/transaction', [
            'json' => $data,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ],
            //'http_errors' => false
        ]);
        return $response;
    }
}
