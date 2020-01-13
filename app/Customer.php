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
}
