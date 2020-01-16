<?php

namespace App;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceTransactionItem extends Model
{
    use SoftDeletes, UsesUuid;

    protected $fillable = [
        'transaction_id', 'name', 'price', 'category', 'washing_service_id', 'drying_service_id', 'other_service_id', 'full_service_id', 'saved', 'earning_points',
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
}
