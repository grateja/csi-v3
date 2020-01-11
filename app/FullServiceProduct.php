<?php

namespace App;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FullServiceProduct extends Model
{
    use SoftDeletes, UsesUuid;

    protected $fillable = [
        'full_service_id', 'product_id', 'name', 'quantity', 'price',
    ];

    public function product() {
        return $this->belongsTo('App\Product');
    }
}
