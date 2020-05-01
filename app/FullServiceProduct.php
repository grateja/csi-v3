<?php

namespace App;

use App\Jobs\AutoSynch;
use App\Traits\UsesSynch;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FullServiceProduct extends Model
{
    use SoftDeletes, UsesUuid, UsesSynch;

    protected $fillable = [
        'full_service_id', 'product_id', 'name', 'quantity', 'price',
    ];

    public function product() {
        return $this->belongsTo('App\Product');
    }

    public function queSynch() {
        return (new AutoSynch('full_service_products', $this->id))->delay(5);
    }
}
