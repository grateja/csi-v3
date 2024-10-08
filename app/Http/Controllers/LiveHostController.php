<?php

namespace App\Http\Controllers;

use App\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client as GuzzleHttpClient;
use Carbon\Carbon;

class LiveHostController extends Controller
{
    // public $shopId = 'f5f13ed0-6598-11ea-9e7b-04922614e2b1';

    public function update() {
        $hasRecord = false;
        if(!($client = $this->getClient())) {
            return false;
        }

        $tables = [
            'customers',
            'customer_dries',
            'customer_washes',
            'expenses',
            'machines',
            'machine_remarks',
            'machine_usages',
            'products',
            'product_purchases',
            'product_transaction_items',
            'rfid_cards',
            'rfid_card_transactions',
            'rfid_load_transactions',
            'service_transaction_items',
            'transactions',
            'transaction_payments',
            'transaction_remarks',
            'store_hours',
            'partial_payments',
            'ti_tos',
            'scarpa_categories',
            'scarpa_variations',
            'scarpa_cleaning_transaction_items',
            'lagoon_transaction_items',
            'lagoon_per_kilo_transaction_items',
            'elux_machines',
            'elux_services',
            'elux_tokens',
            'elux_service_transaction_items',
        ];

        $collections = [];

        foreach($tables as $table) {
            $rec = DB::table($table)
                ->whereNull('synched')
                ->orderBy('created_at')
                ->limit(10)
                ->get();
            $collections[$table] = $rec;
            if($rec->count()) {
                $hasRecord = true;
            }
        }

        if(!$hasRecord) {
            return false;
        }
        // return response()->json($collections);

        $collections['client'] = $client;
        $response = $this->createRequest($collections, $client['id']);

        if($response->getStatusCode() == 200) {
            $result = json_decode($response->getBody());

            foreach($tables as $table) {
                $t = collect($result);
                if(array_key_exists($table . '_id', $t->toArray())) {
                    $updated = $this->setUpdated($table, $t[$table . '_id']);
                }
            }

            return response()->json($result);
        }

        return response()->json([
            'response' => $response,
        ]);
    }

    private function setUpdated($baseTable, $modelIds) {
        $entities = DB::table($baseTable)->whereIn('id', $modelIds);
        $entities->update([
            'synched' => Carbon::now(),
        ]);
        return $entities;
    }

    private function getClient() {
        $client = Client::with('user')->first();
        if($client == null) {
            return false;
        }
        return [
            'id' => $client->user->id,              /// this field will be the shop_id
            'shop_name' => $client->shop_name,
            'owner_name' => $client->user->name,
            'address' => $client->address,
            'owner_email' => $client->user->email,
            'password' => $client->user->password,
        ];
    }

    private function createRequest($data, $shopId) {
        $clientRequest = new GuzzleHttpClient();

        $cloudEndpoint = env('CLOUD_ENDPOINT', 'localhost:8000');

        // $response = $clientRequest->post('http://localhost:8000/api/live/v3/update/' . $shopId, [
        $response = $clientRequest->post("http://$cloudEndpoint/api/live/v3/update/$shopId", [
        // $response = $clientRequest->post('http://csi-v3-live/api/live/v3/update/' . $shopId, [
            'json' => $data,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ],
            //'http_errors' => false
        ]);
        return $response;
    }

    public function forceUnSync() {

        // $tables = [
        //     'customer_dries',
        //     'customer_washes',
        //     'machines',
        //     'machine_remarks',
        //     'machine_usages',
        //     'products',
        //     'product_purchases',
        //     'product_transaction_items',
        //     'service_transaction_items',
        //     'transaction_payments',
        //     'transaction_remarks',
        //     'partial_payments',
        // ];

        // foreach($tables as $table) {
        //     $entities = DB::table($table)->whereDate('updated_at', Carbon::today());
        //     $entities->update([
        //         'synched' => null,
        //     ]);
        // }
    }
}
