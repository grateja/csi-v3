<?php

namespace App;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductTransactionItem extends Model
{
    use SoftDeletes, UsesUuid;

    protected $fillable = [
        'transaction_id', 'name', 'price', 'product_id', 'saved',
    ];

    public function transaction() {
        return $this->belongsTo('App\Transaction');
    }
}
