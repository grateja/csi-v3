<?php

namespace App;

use App\Jobs\AutoSynch;
use App\Traits\UsesSynch;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FullService extends Model
{
    use SoftDeletes, UsesUuid, UsesSynch;

    public $appends = [
        'price',
    ];

    protected $fillable = [
        'name', 'additional_charge', 'discount',
    ];

    public function fullServiceItems() {
        return $this->hasMany('App\FullServiceItem');
    }

    public function fullServiceProducts() {
        return $this->hasMany('App\FullServiceProduct');
    }

    public function getPriceAttribute() {
        return $this->fullServiceItems()->sum('price') + $this->additional_charge - $this->discount + $this->fullServiceProducts()->sum('price');
    }

    public function serviceTransactionItems() {
        return $this->hasMany('App\ServiceTransactionItem');
    }

    protected static function boot() {
        static::deleting(function($model) {
            $model->fullServiceItems()->delete();
            $model->fullServiceProducts()->delete();
            $model->serviceTransactionItems()->where('saved', false)->delete();
        });

        parent::boot();
    }

    public function queSynch() {
        return (new AutoSynch('full_services', $this->id))->delay(5);
    }
}
