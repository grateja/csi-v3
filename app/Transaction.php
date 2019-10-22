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

    protected $fillable = [
        'customer_id', 'job_order',
    ];

    public function products() {
        return $this->hasMany('App\Product');
    }

    public function services() {
        return $this->hasMany('App\Service');
    }

    public function customer() {
        return $this->belongsTo('App\Customer');
    }

    public function payment() {
        return $this->hasOne('App\TransactionPayment');
    }


    public function attachJobOrder() {
        $jobOrder = JobOrderFormat::where('branch_id', $this->branch_id)->first();
        if($jobOrder) {
            $this->job_order = sprintf($jobOrder->format, $jobOrder->start_number);
            $jobOrder->increment('start_number');
        }
    }

    protected static function boot()
    {
        static::deleting(function($model) {
            $model->payment()->delete();

            foreach($model->completedProductTransactions as $product) {
                $product->delete();
            }

            foreach($model->completedServiceTransactions as $service) {
                if($service->available == 0) {
                    return false;
                }
                $service->delete();
            }

            foreach($model->productTransactions as $product) {
                $product->delete();
            }

            foreach($model->serviceTransactions as $service) {
                $service->delete();
            }
        });

        parent::boot();
    }
}
