<?php

namespace App;

use App\Jobs\AutoSynch;
use App\Traits\UsesSynch;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerDry extends Model
{
    use SoftDeletes, UsesUuid, UsesSynch;

    protected $fillable = [
        'service_name', 'customer_id', 'service_transaction_item_id', 'pulse_count', 'minutes', 'dryer_name', 'machine_type', 'used', 'staff_name', 'price', 'synched',
    ];

    public function serviceTransactionItem() {
        return $this->belongsTo('App\ServiceTransactionItem');
    }

    public function customer() {
        return $this->belongsTo('App\Customer');
    }

    public function queSynch() {
        return (new AutoSynch('customer_dries', $this->id))->delay(5);
    }
}
