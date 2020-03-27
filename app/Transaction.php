<?php

namespace App;

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

    public function customer() {
        return $this->belongsTo('App\Customer');
    }

    public function payment() {
        return $this->hasOne('App\TransactionPayment', 'id', 'id');
    }

    public function remarks() {
        return $this->hasMany('App\TransactionRemarks');
    }

    public function posServiceItems() {
        return $this->serviceTransactionItems()->groupBy('name', 'price')->selectRaw('name, COUNT(name) as quantity, SUM(price) as total_price, price as unit_price')->get();
    }

    public function posProductItems() {
        return $this->productTransactionItems()->groupBy('name', 'price', 'product_id')->selectRaw('product_id, name, COUNT(name) as quantity, SUM(price) as total_price, price as unit_price')->get();
    }

    public function getDateStrAttribute() {
        return Carbon::createFromDate($this->date)->format('M-d, Y H:i A');
    }

    public function getDatePaidStrAttribute() {
        if($this->date_paid) {
            return Carbon::createFromDate($this->date_paid)->format('M-d, Y H:i A');
        }
    }

    public function posServiceSummary() {
        // total quantity, total price
        return [
            'total_price' => $this->posServiceItems()->sum('total_price'),
            'total_quantity' => $this->posServiceItems()->sum('quantity'),
        ];
    }

    public function posProductSummary() {
        return [
            'total_price' => $this->posProductItems()->sum('total_price'),
            'total_quantity' => $this->posProductItems()->sum('quantity'),
        ];
    }

    public function attachJobOrder() {
        $jobOrder = JobOrderFormat::first();
        if($jobOrder) {
            $this->job_order = sprintf($jobOrder->format, $jobOrder->start_number);
            $jobOrder->increment('start_number');
        }
    }

    public function totalPrice() {
        $pTotal = $this->posProductItems()->sum('total_price');
        $sTotal = $this->posServiceItems()->sum('total_price');
        return $pTotal + $sTotal;
    }

    public function refreshAll() {
        // $this['customer_name'] = $this->customer->name;
        $this['posServiceItems'] = $this->posServiceItems();
        $this['posProductItems'] = $this->posProductItems();
        $this['posServiceSummary'] = $this->posServiceSummary();
        $this['posProductSummary'] = $this->posProductSummary();
        $this['total_amount'] = $this->posProductSummary()['total_price'] + $this->posServiceSummary()['total_price'];
        $this['paidTo'] = $this->user;
        $this['customer'] = $this->customer;
    }

    public function withPayment() {
        $this['payment'] = $this->payment;
        return $this;
    }

    protected static function boot()
    {
        static::deleting(function($model) {
            $model->payment()->delete();

            foreach ($model->serviceTransactionItems as $value) {
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
}
