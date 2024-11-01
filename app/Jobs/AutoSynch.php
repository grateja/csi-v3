<?php

namespace App\Jobs;

use App\Client;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client as GuzzleHttpClient;

class AutoSynch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $baseTable, $modelId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($baseTable, $modelId)
    {
        $this->baseTable = $baseTable;
        $this->modelId = $modelId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $shopId = Client::first()->user_id;

        $entity = DB::table($this->baseTable)->where('id', $this->modelId)->first();

        $response = $this->createRequest([
            'entity' => $entity,
        ], $shopId);

        if($response->getStatusCode() == 200) {
            DB::table($this->baseTable)->where('id', $this->modelId)->update([
                'synched' => Carbon::now(),
            ]);
        }
    }

    private function createRequest($data, $shopId) {
        $clientRequest = new GuzzleHttpClient();
        $cloudEndpoint = env('CLOUD_ENDPOINT', 'localhost:8000');
        // $response = $clientRequest->post('http://localhost:8000/api/live/v3/update/' . $shopId . '/' . $this->baseTable . '/auto-synch', [
        $response = $clientRequest->post("http://$cloudEndpoint/api/live/v3/update/$shopId/{$this->baseTable}/auto-synch", [
        // $response = $clientRequest->post('http://csi-v3-live/api/live/v3/update/' . $shopId . '/' . $this->baseTable . '/auto-synch', [
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
