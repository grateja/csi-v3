<?php

namespace App;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FullService extends Model
{
    use SoftDeletes, UsesUuid;

    public $appends = [
        'price',
    ];

    protected $fillable = [
        'name', 'additional_charge', 'discount',
    ];

    public function fullServiceItems() {
        return $this->hasMany('App\FullServiceItem');
    }

    public function getPriceAttribute() {
        return $this->fullServiceItems()->sum('price') + $this->additional_charge - $this->discount;
    }
}
