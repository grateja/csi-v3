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
        'id', 'customer_id', 'job_order', 'user_id', 'staff_name', 'date', 'saved', 'customer_name', 'total_price', 'date_paid', 'synched', 'updated_at', 'created_at',
    ];

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

    public function posServiceItems($withTrashed = false) {
        $items = $this->serviceTransactionItems()->groupBy('name', 'price')->selectRaw('name, COUNT(name) as quantity, SUM(price) as total_price, price as unit_price');
        if($withTrashed) {
            $items = $items->withTrashed();
        }
        return $items->get();
    }

    public function posProductItems($withTrashed = false) {
        $items = $this->productTransactionItems()->groupBy('name', 'price', 'product_id')->selectRaw('product_id, name, COUNT(name) as quantity, SUM(price) as total_price, price as unit_price');

        if($withTrashed) {
            $items = $items->withTrashed();
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
        return $pTotal + $sTotal;
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
        $this['posServiceSummary'] = $this->posServiceSummary($withTrashed);
        $this['posProductSummary'] = $this->posProductSummary($withTrashed);
        $totalAmount = $this->posProductSummary($withTrashed)['total_price'] + $this->posServiceSummary($withTrashed)['total_price'];
        $this['paidTo'] = $this->user;
        $this['customer'] = $this->customer;

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
            unset($model['posServiceSummary']);
            unset($model['posProductSummary']);
            unset($model['customer']);
            unset($model['total_amount']);
            unset($model['paidTo']);
            $model['updated_at'] = Carbon::now()->toDateTimeString();
            $model['created_at'] = Carbon::now()->toDateTimeString();
        });

        parent::boot();
    }

    public function queSynch() {
        return (new AutoSynch('transactions', $this->id))->delay(5);
    }
}
