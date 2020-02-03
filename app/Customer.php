<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UsesUuid;

class Customer extends Model
{
    use SoftDeletes, UsesUuid;

    protected $fillable = [
        'name', 'address', 'contact_number', 'email', 'first_visit', 'earned_points',
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
        });

        parent::boot();
    }
}
