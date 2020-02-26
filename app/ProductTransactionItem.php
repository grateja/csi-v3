<?php

namespace App;

use App\Traits\UsesSynch;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductTransactionItem extends Model
{
    use SoftDeletes, UsesUuid, UsesSynch;

    protected $fillable = [
        'transaction_id', 'name', 'price', 'product_id', 'saved', 'synched',
    ];

    public function transaction() {
        return $this->belongsTo('App\Transaction');
    }

    public function product() {
        return $this->belongsTo('App\Product');
    }
}
