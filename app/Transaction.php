<?php

namespace App;

use App\Jobs\AutoSynch;
use App\Traits\UsesSynch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use App\Traits\UsesUuid;
use Carbon\Carbon;

class Transaction extends Model
{
    use SoftDeletes, UsesUuid, UsesSynch;

    protected $fillable = [
        'id', 'customer_id', 'job_order', 'user_id', 'staff_name', 'date', 'saved', 'customer_name', 'total_price', 'date_paid', 'cancelation_remarks', 'synched', 'updated_at', 'created_at',
    ];

    public function simplified() {
        $customer = [
            'nam' => $this->customer->name,
            'crn' => $this->customer->crn,
            'adr' => $this->customer->address,
            'cn' => $this->customer->contact_number,
        ];
        $scarpa = collect($this->posScarpaCleaningItems())->transform(function($item) {
            return "$item->name`$item->quantity`$item->unit_price";
            // return [
            //     'nam' => $item->name,
            //     'qty' => $item->quantity,
            //     'up' => $item->unit_price,
            // ];
        });
        $lagoon = collect($this->posLagoonItems())->transform(function($item) {
            return "$item->name`$item->quantity`$item->unit_price";
            // return [
            //     'nam' => $item->name,
            //     'qty' => $item->quantity,
            //     'up' => $item->unit_price,
            // ];
        });
        $lagoonPerKilo = collect($this->posLagoonPerKiloItems())->transform(function($item) {
            return "$item->name`$item->kilos`$item->price_per_kilo";
            // return [
            //     'nam' => $item->name,
            //     'qty' => $item->kilos,
            //     'up' => $item->price_per_kilo,
            // ];
        });


        if(env('DOPU_INCLUDE_SERVICES', false)) {
            $services = $this->serviceTransactionItems()
                ->groupBy('name', 'price', 'full_service_id', 'category', 'service_id')
                ->selectRaw('coalesce(washing_service_id, drying_service_id, other_service_id) as service_id, COUNT(name) as quantity, category')->get();

            $services = collect($services)->transform(function($item) {
                return "$item->service_id`$item->quantity`$item->category";
                // return [
                //     'sid' => $item->service_id,
                //     'qty' => $item->quantity,
                //     'cat' => $item->category,
                // ];
            });

            $products = collect($this->posProductItems())->transform(function($item) {
                return "$item->product_id`$item->quantity";
                // return [
                //     'pid' => $item->product_id,
                //     'qty' => $item->quantity,
                // ];
            });
        }

        $data = [
            'pid' => Client::first()->user_id,
            'jo' => $this->job_order,
            'cust' => $customer,
            // 'sv' => $scarpa,
            // 'lag' => $lagoon,
            // 'lpk' => $lagoonPerKilo,
        ];
        // if($options['scarpa']) {
            $data['sv'] = $scarpa;
        // }
        // if($options['lagoon']) {
            $data['lag'] = $lagoon;
            $data['lpk'] = $lagoonPerKilo;
        // }
        if(env('DOPU_INCLUDE_SERVICES', false)) {
            $data['prd'] = $products;
            $data['svc'] = $services;
        }

        return json_encode($data);
    }

    public static function filterKeys($val) {
        $keys = [
            'customer_name' => 'Customer name',
            'job_order' => 'Job order number',
            'staff_name' => 'Staff name',
            'date' => 'Date created',
            'date_paid' => 'Date paid',
            'default' => 'date',
        ];

        $filter = collect($keys)->filter(function($value) use ($val, $keys) {
            return strcasecmp($value, $val) == 0 && $keys['default'] != $val;
        })->keys();
        return $filter->first() ?? $keys['default'];
    }

    // public $timestamps = false;

    // public $appends = [
    //     'dateStr',
    //     'datePaidStr',
    // ];

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function serviceTransactionItems() {
        return $this->hasMany('App\ServiceTransactionItem')->orderBy('name');
    }

    public function productTransactionItems() {
        return $this->hasMany('App\ProductTransactionItem')->orderBy('name');
    }

    public function scarpaCleaningTransactionItems() {
        return $this->hasMany('App\ScarpaCleaningTransactionItem')->orderBy('name');
    }

    public function lagoonTransactionItems() {
        return $this->hasMany('App\LagoonTransactionItem')->orderBy('name');
    }

    public function lagoonPerKiloTransactionItems() {
        return $this->hasMany('App\LagoonPerKiloTransactionItem')->orderBy('name');
    }

    public function customerWashes() {
        return $this->hasManyThrough('App\CustomerWash', 'App\ServiceTransactionItem');
    }

    public function customerDries() {
        return $this->hasManyThrough('App\CustomerDry', 'App\ServiceTransactionItem');
    }

    public function customer() {
        return $this->belongsTo('App\Customer');
    }

    public function payment() {
        return $this->hasOne('App\TransactionPayment', 'id', 'id')->withTrashed();
    }

    public function partialPayment() {
        return $this->hasOne('App\PartialPayment');
    }

    public function remarks() {
        return $this->hasMany('App\TransactionRemarks')->orderBy('created_at');
    }

    public function lagoonPartner() {
        return $this->belongsToMany('App\LagoonPartner', 'lagoon_partner_transactions', 'transaction_id', 'lagoon_partner_id');
    }

    public function posServiceItems($withTrashed = false) {
        $items = $this->serviceTransactionItems()
            ->with('fullService.fullServiceItems', 'fullService.fullServiceProducts')
            ->groupBy('name', 'price', 'full_service_id', 'category')
            ->selectRaw('full_service_id, name, COUNT(name) as quantity, SUM(price) as total_price, price as unit_price, category');

        if($withTrashed) {
            $items = $items->withTrashed();
        }
        return $items->get()->transform(function($item) {
            $fullServiceItems = null;
            $fullServiceProducts = null;
            $additionalCharge = 0;
            $discount = 0;
            if($item->fullService) {
                $fullServiceItems = $item->fullService->fullServiceItems->transform(function($_item) use ($item) {
                    return [
                        'id' => $_item->id,
                        'name' => $_item->name,
                        'price' => $_item->price,
                        'quantity' => $_item->quantity * $item->quantity,
                        'total_price' => $_item->price * $item->quantity * $_item->quantity,
                    ];
                });

                $fullServiceProducts = $item->fullService->fullServiceProducts->transform(function($_item) use ($item) {
                    return [
                        'id' => $_item->id,
                        'name' => $_item->name,
                        'price' => $_item->price,
                        'quantity' => $_item->quantity * $item->quantity,
                        'total_price' => $_item->price * $item->quantity * $_item->quantity,
                    ];
                });

                $additionalCharge = $item->fullService->additional_charge;
                $discount = $item->fullService->discount;
            }
            return [
                'name' => $item->name,
                'quantity' => $item->quantity,
                'total_price' => $item->total_price,
                'unit_price' => $item->unit_price,
                'full_service_items' => $fullServiceItems,
                'full_service_products' => $fullServiceProducts,
                'additional_charge' => $additionalCharge,
                'discount' => $discount,
                'category' => $item->category,
            ];
        });
        // return $items->get();
    }

    public function posProductItems($withTrashed = false) {
        $items = $this->productTransactionItems()->groupBy('name', 'price', 'product_id')->selectRaw('product_id, name, COUNT(name) as quantity, SUM(price) as total_price, price as unit_price');

        if($withTrashed) {
            $items = $items->withTrashed();
        }

        return $items->get();
    }

    public function posLagoonItems($withTrashed = false) {
        $items = $this->lagoonTransactionItems()->groupBy('name', 'price', 'lagoon_id')->selectRaw('lagoon_id, name, COUNT(name) as quantity, SUM(price) as total_price, price as unit_price');

        if($withTrashed) {
            $items = $items->withTrashed();
        }

        return $items->get();
    }

    public function posLagoonPerKiloItems($withTrashed = false) {
        // $items = $this->lagoonPerKiloTransactionItems()->groupBy('name', 'total_price', 'lagoon_per_kilo_id')->selectRaw('lagoon_per_kilo_id, name, COUNT(name) as quantity, SUM(price) as total_price, price as unit_price');
        $items = $this->lagoonPerKiloTransactionItems()
            ->select('id', 'name', 'kilos', 'price_per_kilo', 'total_price', 'lagoon_per_kilo_id');

        if($withTrashed) {
            $items = $items->withTrashed();
        }

        return $items->get();
    }

    public function posScarpaCleaningItems($withTrashed = false) {
        $items = $this->scarpaCleaningTransactionItems()->groupBy('scarpa_variation_id', 'name', 'price')
            ->selectRaw('scarpa_variation_id, name, COUNT(name) as quantity, SUM(price) as total_price, price as unit_price');

        if($withTrashed) {
            $items->withTrashed();
        }

        return $items->get();
    }

    // public function posServiceItemsWithTrashed() {
    //     return $this->serviceTransactionItems()->withTrashed()->groupBy('name', 'price')->selectRaw('name, COUNT(name) as quantity, SUM(price) as total_price, price as unit_price')->get();
    // }

    // public function posProductItemsWithTrashed() {
    //     return $this->productTransactionItems()->withTrashed()->groupBy('name', 'price', 'product_id')->selectRaw('product_id, name, COUNT(name) as quantity, SUM(price) as total_price, price as unit_price')->get();
    // }

    public function getDateStrAttribute() {
        return Carbon::createFromDate($this->date)->format('M-d, Y H:i A');
    }

    public function getDatePaidStrAttribute() {
        if($this->date_paid) {
            return Carbon::createFromDate($this->date_paid)->format('M-d, Y H:i A');
        }
    }

    public function posServiceSummary($withTrashed = false) {
        // total quantity, total price
        return [
            'total_price' => $this->posServiceItems($withTrashed)->sum('total_price'),
            'total_quantity' => $this->posServiceItems($withTrashed)->sum('quantity'),
        ];
    }

    public function posProductSummary($withTrashed = false) {
        return [
            'total_price' => $this->posProductItems($withTrashed)->sum('total_price'),
            'total_quantity' => $this->posProductItems($withTrashed)->sum('quantity'),
        ];
    }

    public function posScarpaCleaningSummary($withTrashed = false) {
        return [
            'total_price' => $this->posScarpaCleaningItems($withTrashed)->sum('total_price'),
            'total_quantity' => $this->posScarpaCleaningItems($withTrashed)->sum('quantity'),
        ];
    }

    public function posLagoonSummary($withTrashed = false) {
        return [
            'total_price' => $this->posLagoonItems($withTrashed)->sum('total_price'),
            'total_quantity' => $this->posLagoonItems($withTrashed)->sum('quantity'),
        ];
    }

    public function posLagoonPerKiloSummary($withTrashed = false) {
        return [
            'total_price' => $this->posLagoonPerKiloItems($withTrashed)->sum('total_price'),
            'total_quantity' => $this->posLagoonPerKiloItems($withTrashed)->sum('kilos'),
        ];
    }

    public function attachJobOrder() {
        $jobOrder = JobOrderFormat::first();
        if(!$jobOrder) {
            JobOrderFormat::create();
            $jobOrder = JobOrderFormat::first();
        }
        $this->job_order = sprintf($jobOrder->format, $jobOrder->start_number);
        $jobOrder->increment('start_number');
    }

    public function totalPrice() {
        $pTotal = $this->posProductItems()->sum('total_price');
        $sTotal = $this->posServiceItems()->sum('total_price');
        $scTotal = $this->posScarpaCleaningItems()->sum('total_price');
        $lTotal = $this->posLagoonItems()->sum('total_price');
        $lpkTotal = $this->posLagoonPerKiloItems()->sum('total_price');
        return $pTotal + $sTotal + $scTotal + $lTotal + $lpkTotal; 
    }

    // public function refreshAllWithTrashed() {
    //     $this['posServiceItems'] = $this->posServiceItemsWithTrashed();
    //     $this['posProductItems'] = $this->posProductItemsWithTrashed();
    //     $this['posServiceSummary'] = $this->posServiceSummary();
    //     $this['posProductSummary'] = $this->posProductSummary();
    //     $this['total_amount'] = $this->posProductSummary()['total_price'] + $this->posServiceSummary()['total_price'];
    //     $this['paidTo'] = $this->user;
    //     $this['customer'] = $this->customer;
    // }

    public function refreshAll($withTrashed = false) {
        // $this['customer_name'] = $this->customer->name;
        $this['posServiceItems'] = $this->posServiceItems($withTrashed);
        $this['posProductItems'] = $this->posProductItems($withTrashed);
        $this['posScarpaCleaningItems'] = $this->posScarpaCleaningItems($withTrashed);
        $this['posLagoonItems'] = $this->posLagoonItems($withTrashed);
        $this['posLagoonPerKiloItems'] = $this->posLagoonPerKiloItems($withTrashed);
        $this['posServiceSummary'] = $this->posServiceSummary($withTrashed);
        $this['posProductSummary'] = $this->posProductSummary($withTrashed);
        $this['posScarpaCleaningSummary'] = $this->posScarpaCleaningSummary($withTrashed);
        $this['posLagoonSummary'] = $this->posLagoonSummary($withTrashed);
        $this['posLagoonPerKiloSummary'] = $this->posLagoonPerKiloSummary($withTrashed);
        $totalAmount = $this->posProductSummary($withTrashed)['total_price'] + $this->posServiceSummary($withTrashed)['total_price'] + $this->posScarpaCleaningSummary($withTrashed)['total_price'] + $this->posLagoonSummary($withTrashed)['total_price'] + $this->posLagoonPerKiloSummary($withTrashed)['total_price'];
        if($this->payment) {
            $this['paidTo'] = $this->payment->user->name;
        }
        $this['customer'] = $this->customer;
        $this['remarks'] = $this->remarks;

        if($this->total_price != $totalAmount) {
            $this->update(['total_price' => $totalAmount]);
            $this->refreshAll($withTrashed);
        }
    }

    public function withPayment() {
        $this['payment'] = $this->payment;
        $this['partial_payment'] = $this->partialPayment;
        return $this;
    }

    protected static function boot()
    {
        static::deleting(function($model) {
            $model->payment()->delete();
            $model->partialPayment()->delete();

            foreach ($model->serviceTransactionItems as $value) {
                if($value->category == 'full') {
                    $productItems = $value->fullService->fullServiceProducts;
                    foreach($productItems as $productItem) {
                        $product = Product::find($productItem->product_id);
                        if($product) {
                            $product->increment('current_stock', $productItem->quantity);
                        }
                    }
                }
                $value->delete();
            }

            foreach ($model->productTransactionItems as $value) {
                $value->product()->increment('current_stock');
                $value->delete();
            }

            // foreach($model->completedProductTransactions as $product) {
            //     $product->delete();
            // }

            // foreach($model->completedServiceTransactions as $service) {
            //     if($service->available == 0) {
            //         return false;
            //     }
            //     $service->delete();
            // }

            // foreach($model->productTransactions as $product) {
            //     $product->delete();
            // }

            // foreach($model->serviceTransactions as $service) {
            //     $service->delete();
            // }
        });

        static::updating(function($model) {
            unset($model['posServiceItems']);
            unset($model['posProductItems']);
            unset($model['posScarpaCleaningItems']);
            unset($model['posServiceSummary']);
            unset($model['posLagoonItems']);
            unset($model['posLagoonSummary']);
            unset($model['posLagoonPerKiloItems']);
            unset($model['posLagoonPerKiloSummary']);
            unset($model['posScarpaCleaningSummary']);
            unset($model['posProductSummary']);
            unset($model['customer']);
            unset($model['total_amount']);
            unset($model['paidTo']);
            unset($model['remarks']);
            $model['updated_at'] = Carbon::now()->toDateTimeString();
            $model['created_at'] = Carbon::now()->toDateTimeString();
        });

        static::created(function($model) {
            $monitorChecker = MonitorChecker::firstOrCreate(['id' => 'default']);
            $monitorChecker->update([
                'transaction_id' => $model->id,
                'token' => $model->id,
                'action' => 'retreive',
            ]);
        });

        parent::boot();
    }

    public function queSynch() {
        return (new AutoSynch('transactions', $this->id))->delay(5);
    }
}
