<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UsesUuid;

class ProductPurchase extends Model
{
    use SoftDeletes, UsesUuid;

    protected $fillable = [
        'date', 'product_id', 'receipt', 'quantity', 'unit_cost', 'remarks', 'user_id',
    ];

    public function product() {
        return $this->belongsTo('App\Product');
    }

    public function user() {
        return $this->belongsTo('App\User');
    }
}
