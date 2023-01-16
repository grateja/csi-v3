<?php

namespace App\Jobs;

use App\Client;
use App\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client as GuzzleHttpClient;

class SendShopPreferences implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $shop = Client::first();
        $admin = User::whereHas('roles', function($query) {
            $query->where('name', 'admin');
        })->first();

        $data = [
            'id' => $shop->user_id,
            'owner_name' => $admin->name,
            'shop_name' => $shop->shop_name,
            'address' => $shop->address,
        ];


        $response = $this->createRequest([
            'shopPreferences' => $data,
            'admin' => [
                'name' => $admin->name,
                'email' => $admin->email,
                'password' => $admin->password,
            ],
        ], $shop->user_id);
    }

    private function createRequest($data, $shopId) {
        $clientRequest = new GuzzleHttpClient();
        $response = $clientRequest->post('http://139.162.73.87/api/live/v3/update/' . $shopId . '/shop-preferences', [
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
