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
        'id', 'remarks','crn', 'name', 'address', 'contact_number', 'email', 'organization', 'first_visit', 'earned_points', 'synched',
    ];

    public static function filterKeys($val) {
        $keys = [
            'crn' => 'crn',
            'remarks' => 'remarks',
            'name' => 'name',
            'first_visit' => 'birthday',
            'DATE_FORMAT(first_visit, "%m-%d")' => 'Birthday w/o Year',
            'customer_washes_count' => 'Total Washes',
            'customer_dries_count' => 'Total Dries',
            'created_at' => 'first visit',
            'earned_points' => 'Current points',
            'default' => 'name',
        ];

        $filter = collect($keys)->filter(function($value) use ($val, $keys) {
            return strcasecmp($value, $val) == 0 && $keys['default'] != $val;
        })->keys();
        return $filter->first() ?? $keys['default'];
    }

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

    public function lagoonPartner() {
        return $this->belongsToMany('App\LagoonPartner', 'lagoon_partner_customers', 'customer_id', 'lagoon_partner_id');
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
