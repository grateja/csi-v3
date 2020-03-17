<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client as GuzzleHttpClient;
use Carbon\Carbon;

class LiveHostController extends Controller
{
    public $shopId = 'f5f13ed0-6598-11ea-9e7b-04922614e2b1';

    public function update() {
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
        ];

        $collections = [];

        foreach($tables as $table) {
            $collections[$table] = DB::table($table)
                ->whereNull('synched')
                ->orderByDesc('created_at')
                ->limit(25)
                ->get();
        }

        $response = $this->createRequest($collections);

        if($response->getStatusCode() == 200) {
            $result = json_decode($response->getBody());

            foreach($tables as $table) {
                $t = collect($result);
                $updated = $this->setUpdated($table, $t[$table . '_id']);
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

    private function createRequest($data) {
        $clientRequest = new GuzzleHttpClient();
        $response = $clientRequest->post('http://csi-v3-live/api/live/update/' . $this->shopId, [
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
