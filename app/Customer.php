<?php

namespace App;

use App\Traits\UsesSynch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UsesUuid;
use App\Jobs\AutoSynch;

class Customer extends Model
{
    use SoftDeletes, UsesUuid, UsesSynch;

    protected $fillable = [
        'name', 'address', 'contact_number', 'email', 'first_visit', 'earned_points', 'synched',
    ];

    public function setNameAttribute($value) {
        $this->attributes['name'] = ucwords($value);
    }

    public function transactions() {
        return $this->hasMany('App\Transaction');
    }

    public function rfidCards() {
        return $this->hasMany('App\RfidCard');
    }

    public function customerDries() {
        return $this->hasMany('App\CustomerDry');
    }

    public function customerWashes() {
        return $this->hasMany('App\CustomerWash');
    }

    protected static function boot() {
        static::deleting(function($model) {
            $model->customerDries()->delete();
            $model->customerWashes()->delete();
            $model->rfidCards()->delete();
            $model->transactions()->whereNull('date_paid')->delete();
        });

        parent::boot();
    }

    public function queSynch() {
        return (new AutoSynch('customers', $this->id))->delay(5);
    }
}
