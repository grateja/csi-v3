<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UsesUuid;

class ProductPurchase extends Model
{
    use SoftDeletes, UsesUuid;

    protected $fillable = [
        'date', 'product_id', 'product_name', 'receipt', 'quantity', 'unit_cost', 'remarks', 'staff_name',
    ];

    public $timestamps = false;

    public function product() {
        return $this->belongsTo('App\Product');
    }

    public function user() {
        return $this->belongsTo('App\User');
    }
}
