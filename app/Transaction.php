<?php

namespace App;

use App\Traits\UsesSynch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use App\Traits\UsesUuid;

class Transaction extends Model
{
    use SoftDeletes, UsesUuid, UsesSynch;

    public $timestamps = false;

    protected $fillable = [
        'customer_id', 'job_order', 'user_id', 'date', 'saved',
    ];

    public function serviceTransactionItems() {
        return $this->hasMany('App\ServiceTransactionItem')->orderBy('name');
    }

    public function customer() {
        return $this->belongsTo('App\Customer');
    }

    public function payment() {
        return $this->hasOne('App\TransactionPayment');
    }

    public function posServiceItems() {
        return $this->serviceTransactionItems()->groupBy('name', 'price')->selectRaw('name, COUNT(name) as quantity, SUM(price) as total_price, price as unit_price')->get();
    }

    public function posServiceSummary() {
        // total quantity, total price
        return [
            'total_price' => $this->posServiceItems->sum('total_price'),
            'total_quantity' => $this->posServiceItems->sum('quantity'),
        ];
    }

    public function attachJobOrder() {
        $jobOrder = JobOrderFormat::first();
        if($jobOrder) {
            $this->job_order = sprintf($jobOrder->format, $jobOrder->start_number);
            $jobOrder->increment('start_number');
        }
    }

    protected static function boot()
    {
        static::deleting(function($model) {
            $model->payment()->delete();

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

        parent::boot();
    }
}
