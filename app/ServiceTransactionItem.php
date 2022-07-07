<?php

namespace App;

use App\Jobs\AutoSynch;
use App\Traits\UsesSynch;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceTransactionItem extends Model
{
    use SoftDeletes, UsesUuid, UsesSynch;

    protected $fillable = [
        'transaction_id', 'name', 'price', 'category', 'washing_service_id', 'drying_service_id', 'other_service_id', 'full_service_id', 'saved', 'earning_points', 'synched',
    ];

    public function washingService() {
        return $this->belongsTo('App\WashingService');
    }

    public function dryingService() {
        return $this->belongsTo('App\DryingService');
    }

    public function fullService() {
        return $this->belongsTo('App\FullService');
    }

    public function customerWash() {
        return $this->hasOne('App\CustomerWash');
    }

    public function customerDry() {
        return $this->hasOne('App\CustomerDry');
    }

    public function transaction() {
        return $this->belongsTo('App\Transaction');
    }

    public function machineName() {
        if($this->category == 'washing' && $this->saved) {
            return $this->customerWash->washer_name;
        } else if($this->category == 'drying' && $this->saved) {
            return $this->customerDry->dryer_name;
        }
        return null;
    }

    public function used() {
        if($this->category == 'washing' && $this->saved) {
            return $this->customerWash->used;
        } else if($this->category == 'drying' && $this->saved) {
            return $this->customerDry->used;
        }
        return null;
    }

    protected static function boot() {
        static::deleting(function($model) {
            $model->customerDry()->delete();
            $model->customerWash()->delete();
            $monitorChecker = MonitorChecker::firstOrCreate(['id' => 'default']);
            $monitorChecker->update([
                'transaction_id' => $model->transaction_id,
                'token' => $model->id,
                'action' => 'remove-service',
            ]);
        });
        static::created(function($model) {
            $monitorChecker = MonitorChecker::firstOrCreate(['id' => 'default']);
            $monitorChecker->update([
                'transaction_id' => $model->transaction_id,
                'token' => $model->id,
                'action' => 'add-service',
            ]);
        });
        parent::boot();
    }

    public function queSynch() {
        return (new AutoSynch('service_transaction_items', $this->id))->delay(5);
    }
}
