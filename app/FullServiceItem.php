<?php

namespace App;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FullServiceItem extends Model
{
    use SoftDeletes, UsesUuid;

    protected $fillable = [
        'full_service_id', 'category', 'name', 'price', 'quantity', 'points', 'washing_service_id', 'drying_service_id', 'other_service_id',
    ];

    public function washingService() {
        return $this->belongsTo('App\WashingService');
    }

    public function dryingService() {
        return $this->belongsTo('App\DryingService');
    }

    public function otherService() {
        return $this->belongsTo('App\OtherService');
    }

    public function customerWash() {
        return $this->hasOne('App\CustomerWash');
    }

    public function name() {
        if($this->category == 'washing') {
            return $this->washingService->name;
        } else if($this->category == 'drying') {
            return $this->dryingService->name;
        } else if($this->category == 'other') {
            return $this->otherService->name;
        }
    }
}