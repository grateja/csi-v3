<?php

namespace App;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerWash extends Model
{
    use SoftDeletes, UsesUuid;

    protected $fillable = [
        'service_name', 'customer_id', 'service_transaction_item_id', 'pulse_count', 'minutes', 'washer_name', 'machine_type', 'used', 'staff_name', 'price',
    ];

    public function serviceTransactionItem() {
        return $this->belongsTo('App\ServiceTransactionItem');
    }
}
