<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use App\CustomerWash;
use App\CustomerDry;

class AutoDeleteWashDry implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $baseTable, $modelId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($modelId)
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
        $entity = DB::table($this->baseTable)->where('id', $this->modelId)->first();
        if($entity != null && $entity->tries > 0 && Carbon::now()->diffInDays($entity->created_at) > 3) {
            $entity->delete();
        }

        // $response = $this->createRequest([
        //     'entity' => $entity,
        // ], $shopId);

        // if($response->getStatusCode() == 200) {
        //     DB::table($this->baseTable)->where('id', $this->modelId)->update([
        //         'synched' => Carbon::now(),
        //     ]);
        // }
    }

    // private function createRequest($data, $shopId) {
    //     $clientRequest = new GuzzleHttpClient();
    //     // $response = $clientRequest->post('http://localhost:8000/api/live/v3/update/' . $shopId . '/' . $this->baseTable . '/auto-synch', [
    //     $response = $clientRequest->post('http://139.162.73.87/api/live/v3/update/' . $shopId . '/' . $this->baseTable . '/auto-synch', [
    //     // $response = $clientRequest->post('http://csi-v3-live/api/live/v3/update/' . $shopId . '/' . $this->baseTable . '/auto-synch', [
    //             'json' => $data,
    //         'headers' => [
    //             'Content-Type' => 'application/json',
    //             'Accept' => 'application/json'
    //         ],
    //         //'http_errors' => false
    //     ]);
    //     return $response;
    // }
}
