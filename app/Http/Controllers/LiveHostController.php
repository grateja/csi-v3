<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Barangay;
use App\CityMunicipality;
use Illuminate\Support\Facades\DB;
use App\Client;
use App\User;
use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\RequestOptions;
use PhpOffice\PhpSpreadsheet\Exception;
use Carbon\Carbon;
use App\Branch;
use App\Product;
use App\BranchProduct;
use App\ProductPurchase;
use App\Machine;
use App\Discount;
use App\JobOrderFormat;
use App\LoyaltyPoint;
use App\Expense;
use App\Customer;
use App\Service;
use App\BranchService;
use App\Transaction;
use App\ServiceTransaction;
use App\ProductTransaction;
use App\CompletedProductTransaction;
use App\CompletedServiceTransaction;
use App\ServiceItemRemarks;
use App\ProductItemRemarks;
use App\TransactionPayment;
use App\TransactionRemarks;
use App\RfidCard;
use App\RfidLoadTransaction;
use App\RfidServicePrice;
use App\RfidPosTransaction;
use App\RfidTransaction;
use App\Jobs\Synch;

class LiveHostController extends Controller
{
    public function test() {
        $user = User::where('email', 'admin@gmail.com')->first();

        dispatch((new Synch($user))->delay(Carbon::now()->addSeconds(20)));
    }

    public function registerOwner() {
        $owner = User::with('client')->whereHas('roles', function($query) {
            $query->where('id', 2);
        })->first();

        $client = new GuzzleHttpClient();

        // $response = $client->post('http://localhost:8000/api/live/register-owner', [
        $response = $client->post('http://139.162.73.87/api/live/register-owner', [
            'json' => $owner,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ],
            'http_errors' => false
        ]);

        $result = json_decode($response->getBody());
        if($response->getStatusCode() == 422) {


            if($result->action == 'updateId') {
                $users = User::where('client_id', $result->localId);
                $users->update([
                    'client_id' => $result->liveId,
                ]);
                $owner->update([
                    'id' => $result->liveId,
                ]);

                return response()->json([
                    'users' => $users,
                    'owner' => $owner,
                    'liveId' => $result->liveId,
                    'localId' => $result->localId,
                ], 200);
            }
        }

        if($result->email) {
            $this->upload($result->email);
        }

        return $response;
    }

    private function createRequest($clientId, $data) {
        $clientRequest = new GuzzleHttpClient();
        // $response = $clientRequest->post('http://localhost:8000/api/live/update/' . $clientId, [
        $response = $clientRequest->post('http://139.162.73.87/api/live/update/' . $clientId, [
            'json' => $data,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ],
            //'http_errors' => false
        ]);
        return $response;
    }

    public function upload($ownerEmail) {
        $owner = User::withTrashed()->with(['branches' => function($query) {
            $query->withTrashed()->whereNull('branches.synched');
        }])->where('email', $ownerEmail)
            ->first();
        if($owner == null) {
            return response()->json([
                'errors' => [
                    'email' => ['Invalid email or unregistered']
                ],
            ], 422);
        }






        $users = User::withTrashed()->with(['barangay' => function($query) {
            $query->withTrashed();
        }, 'cityMunicipality' => function($query) {
            $query->withTrashed();
        }])
            ->whereNull('synched')
            ->where('client_id', $owner->client_id)
            ->get();


        $products = Product::where('client_id', $owner->id)
            ->whereNull('synched')->withTrashed()->limit(10)->get();

        $branchProducts = BranchProduct::whereHas('product', function($query) use ($owner) {
            $query->where('client_id', $owner->id)->withTrashed();
        })->whereNull('synched')->limit(10)->get();

        $productPurchases = ProductPurchase::whereHas('branchProduct', function($query) use ($owner) {
            $query->whereHas('product', function($query) use ($owner) {
                $query->where('client_id', $owner->id)->withTrashed();
            })->withTrashed();
        })->whereNull('synched')->withTrashed()->limit(10)->get();


        $customers = Customer::where('client_id', $owner->id)
            ->whereNull('synched')->withTrashed()->limit(10)->get();

        $machines = Machine::where('client_id', $owner->id)
            ->whereNull('synched')->withTrashed()->limit(10)->get();

        $discounts = Discount::whereHas('branch', function($query) use ($owner) {
            $query->where('client_id', $owner->id)->withTrashed();
        })->whereNull('synched')->withTrashed()->limit(10)->get();

        $jobOrderFormats = JobOrderFormat::whereHas('branch', function($query) use ($owner) {
            $query->where('client_id', $owner->id)->withTrashed();
        })->whereNull('synched')->withTrashed()->limit(10)->get();

        $loyaltyPoints = LoyaltyPoint::whereHas('branch', function($query) use ($owner) {
            $query->where('client_id', $owner->id)->withTrashed();
        })->whereNull('synched')->withTrashed()->limit(10)->get();

        $expenses = Expense::where('client_id', $owner->id)
            ->whereNull('synched')->withTrashed()->limit(10)->get();

        $services = Service::where('client_id', $owner->id)
            ->whereNull('synched')->withTrashed()->limit(10)->get();

        $branchServices = BranchService::whereHas('branch', function($query) use ($owner) {
            $query->where('client_id', $owner->id)->withTrashed();
        })->whereNull('synched')->withTrashed()->limit(10)->get();

        $transactions = Transaction::whereHas('branch', function($query) use ($owner) {
            $query->where('client_id', $owner->id)->withTrashed();
        })->whereNull('synched')->withTrashed()->limit(10)->get();

        $serviceTransactions = ServiceTransaction::whereHas('transaction', function($query) use ($owner) {
            $query->whereHas('branch', function($query) use ($owner) {
                $query->where('client_id', $owner->id)->withTrashed();
            })->withTrashed();
        })->whereNull('synched')->withTrashed()->limit(10)->get();

        $productTransactions = ProductTransaction::whereHas('transaction', function($query) use ($owner) {
            $query->whereHas('branch', function($query) use ($owner) {
                $query->where('client_id', $owner->id)->withTrashed();
            })->withTrashed();
        })->whereNull('synched')->withTrashed()->limit(10)->get();

        $completedProductTransactions = CompletedProductTransaction::whereHas('transaction', function($query) use ($owner) {
            $query->whereHas('branch', function($query) use ($owner) {
                $query->where('client_id', $owner->id)->withTrashed();
            })->withTrashed();
        })->whereNull('synched')->withTrashed()->limit(10)->get();

        $completedServiceTransactions = CompletedServiceTransaction::whereHas('transaction', function($query) use ($owner) {
            $query->whereHas('branch', function($query) use ($owner) {
                $query->where('client_id', $owner->id)->withTrashed();
            })->withTrashed();
        })->whereNull('synched')->withTrashed()->limit(10)->get();

        $serviceItemRemarks = ServiceItemRemarks::whereHas('transaction', function($query) use ($owner) {
            $query->whereHas('branch', function($query) use ($owner) {
                $query->where('client_id', $owner->id)->withTrashed();
            })->withTrashed();
        })->whereNull('synched')->withTrashed()->limit(10)->get();

        $productItemRemarks = ProductItemRemarks::whereHas('transaction', function($query) use ($owner) {
            $query->whereHas('branch', function($query) use ($owner) {
                $query->where('client_id', $owner->id)->withTrashed();
            })->withTrashed();
        })->whereNull('synched')->withTrashed()->limit(10)->get();

        $transactionPayments = TransactionPayment::whereHas('transaction', function($query) use ($owner) {
            $query->whereHas('branch', function($query) use ($owner) {
                $query->where('client_id', $owner->id)->withTrashed();
            })->withTrashed();
        })->whereNull('synched')->withTrashed()->limit(10)->get();

        $transactionRemarks = TransactionRemarks::whereHas('transaction', function($query) use ($owner) {
            $query->whereHas('branch', function($query) use ($owner) {
                $query->where('client_id', $owner->id)->withTrashed();
            })->withTrashed();
        })->whereNull('synched')->withTrashed()->limit(10)->get();

        $rfidCards = RfidCard::where('client_id', $owner->id)
            ->whereNull('synched')
            ->withTrashed()->get();

        $rfidLoadTransactions = RfidLoadTransaction::whereHas('rfidCard', function($query) use ($owner) {
                $query->withTrashed()->where('client_id', $owner->id);
            })
            ->whereNull('synched')
            ->withTrashed()->limit(10)->get();

        $rfidPosTransactions = RfidPosTransaction::whereHas('rfidCard', function($query) use ($owner) {
                $query->withTrashed()->where('client_id', $owner->id);
            })
            ->whereNull('synched')
            ->withTrashed()->limit(10)->get();

        $rfidServicePrices = RfidServicePrice::where('client_id', $owner->id)
            ->whereNull('synched')
            ->withTrashed()->limit(10)->get();

        $rfidTransactions = RfidTransaction::where('client_id', $owner->id)
            ->whereNull('synched')
            ->withTrashed()->limit(10)->get();







        $data = [
            'users' => $users,
            'owner' => $owner,
            'products' => $products,
            'branchProducts' => $branchProducts,
            'productPurchases' => $productPurchases,
            'customers' => $customers,
            'machines' => $machines,
            'discounts' => $discounts,
            'jobOrderFormats' => $jobOrderFormats,
            'loyaltyPoints' => $loyaltyPoints,
            'expenses' => $expenses,
            'services' => $services,
            'branchServices' => $branchServices,
            'transactions' => $transactions,
            'serviceTransactions' => $serviceTransactions,
            'productTransactions' => $productTransactions,
            'completedServiceTransactions' => $completedServiceTransactions,
            'completedProductTransactions' => $completedProductTransactions,
            'serviceItemRemarks' => $serviceItemRemarks,
            'productItemRemarks' => $productItemRemarks,
            'transactionPayments' => $transactionPayments,
            'transactionRemarks' => $transactionRemarks,
            'rfidCards' => $rfidCards,
            'rfidLoadTransactions' => $rfidLoadTransactions,
            'rfidPosTransactions' => $rfidPosTransactions,
            'rfidServicePrices' => $rfidServicePrices,
            'rfidTransactions' => $rfidTransactions,
        ];

        $response = $this->createRequest($owner->id, $data);

        if($response->getStatusCode() == 422) {
            return $response;
            // return json_decode($response->getBody())->errorCode;
        } else if($response->getStatusCode() == 200) {

            return DB::transaction(function () use ($response, $owner) {

                $result = json_decode($response->getBody());
                if(isset($result->userIds)) {
                    User::whereIn('id', $result->userIds)->update([
                        'synched' => Carbon::now(),
                    ]);
                }

                if(isset($result->branchIds)) {
                    Branch::whereIn('id', $result->branchIds)->update([
                        'synched' => Carbon::now(),
                    ]);
                }

                // update client synched
                $owner->client->update([
                    'synched' => Carbon::now(),
                ]);

                Product::whereIn('id', $result->productIds)->update([
                    'synched' => Carbon::now(),
                ]);

                BranchProduct::whereIn('id', $result->branchProductIds)->update([
                    'synched' => Carbon::now(),
                ]);

                ProductPurchase::whereIn('id', $result->productPurchaseIds)->update([
                    'synched' => Carbon::now(),
                ]);

                Customer::whereIn('id', $result->customerIds)->update([
                    'synched' => Carbon::now(),
                ]);

                Machine::whereIn('id', $result->machineIds)->update([
                    'synched' => Carbon::now(),
                ]);

                Discount::whereIn('id', $result->discountIds)->update([
                    'synched' => Carbon::now(),
                ]);

                JobOrderFormat::whereIn('id', $result->jobOrderFormatIds)->update([
                    'synched' => Carbon::now(),
                ]);

                LoyaltyPoint::whereIn('id', $result->loyaltyPointIds)->update([
                    'synched' => Carbon::now(),
                ]);

                Expense::whereIn('id', $result->expenseIds)->update([
                    'synched' => Carbon::now(),
                ]);

                Service::whereIn('id', $result->serviceIds)->update([
                    'synched' => Carbon::now(),
                ]);

                BranchService::whereIn('id', $result->branchServiceIds)->update([
                    'synched' => Carbon::now(),
                ]);


                Transaction::whereIn('id', $result->transactionIds)->withTrashed()->update([
                    'synched' => Carbon::now(),
                ]);

                ServiceTransaction::whereIn('id', $result->serviceTransactionIds)->withTrashed()->update([
                    'synched' => Carbon::now(),
                ]);

                ProductTransaction::whereIn('id', $result->productTransactionIds)->withTrashed()->update([
                    'synched' => Carbon::now(),
                ]);

                CompletedServiceTransaction::whereIn('id', $result->completedServiceTransactionIds)->withTrashed()->update([
                    'synched' => Carbon::now(),
                ]);

                CompletedProductTransaction::whereIn('id', $result->completedProductTransactionIds)->withTrashed()->update([
                    'synched' => Carbon::now(),
                ]);

                ServiceItemRemarks::whereIn('id', $result->serviceItemRemarkIds)->withTrashed()->update([
                    'synched' => Carbon::now(),
                ]);

                ProductItemRemarks::whereIn('id', $result->productItemRemarkIds)->withTrashed()->update([
                    'synched' => Carbon::now(),
                ]);

                TransactionPayment::whereIn('transaction_id', $result->transactionPaymentIds)->withTrashed()->update([
                    'synched' => Carbon::now(),
                ]);

                TransactionRemarks::whereIn('id', $result->transactionRemarkIds)->withTrashed()->update([
                    'synched' => Carbon::now(),
                ]);

                RfidCard::whereIn('id', $result->rfidCardIds)->withTrashed()->update([
                    'synched' => Carbon::now(),
                ]);

                RfidLoadTransaction::whereIn('id', $result->rfidLoadTransactionIds)->withTrashed()->update([
                    'synched' => Carbon::now(),
                ]);

                RfidPosTransaction::whereIn('id', $result->rfidPosTransactionIds)->withTrashed()->update([
                    'synched' => Carbon::now(),
                ]);

                RfidTransaction::whereIn('id', $result->rfidTransactionIds)->withTrashed()->update([
                    'synched' => Carbon::now(),
                ]);

                RfidServicePrice::whereIn('id', $result->rfidServicePriceIds)->withTrashed()->update([
                    'synched' => Carbon::now(),
                ]);



















                return response()->json([
                    'userIds' => $result->userIds,
                    'branchIds' => $result->branchIds,
                    'productIds' => $result->productIds,
                    'branchProductIds' => $result->branchProductIds,
                    'productPurchaseIds' => $result->productPurchaseIds,
                    'machineIds' => $result->machineIds,
                    'customerIds' => $result->customerIds,
                    'discountIds' => $result->discountIds,
                    'jobOrderFormatIds' => $result->jobOrderFormatIds,
                    'loyaltyPointIds' => $result->loyaltyPointIds,
                    'expenseIds' => $result->expenseIds,
                    'serviceIds' => $result->serviceIds,
                    'branchServiceIds' => $result->branchServiceIds,
                    'transactionIds' => $result->transactionIds,
                    'serviceTransactionIds' => $result->serviceTransactionIds,
                    'productTransactionIds' => $result->productTransactionIds,
                    'completedServiceTransactionIds' => $result->completedServiceTransactionIds,
                    'completedProductTransactionIds' => $result->completedProductTransactionIds,
                    'serviceItemRemarkIds' => $result->serviceItemRemarkIds,
                    'productItemRemarkIds' => $result->productItemRemarkIds,
                    'transactionPaymentIds' => $result->transactionPaymentIds,
                    'transactionRemarkIds' => $result->transactionRemarkIds,
                    'rfidCardIds' => $result->rfidCardIds,
                    'rfidLoadTransactionIds' => $result->rfidLoadTransactionIds,
                    'rfidPosTransactionIds' => $result->rfidPosTransactionIds,
                    'rfidTransactionIds' => $result->rfidTransactionIds,
                    'rfidServicePriceIds' => $result->rfidServicePriceIds,
                ], 200);


            });

        }


        return $response;
    }
}
