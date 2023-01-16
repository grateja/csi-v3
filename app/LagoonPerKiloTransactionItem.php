<?php

namespace App;

use App\Traits\UsesSynch;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LagoonPerKiloTransactionItem extends Model
{
    use SoftDeletes, UsesUuid, UsesSynch;

    protected $fillable = [
        'transaction_id', 'name', 'kilos', 'price_per_kilo', 'lagoon_per_kilo_id', 'total_price', 'saved', 'synched',
    ];

    public function transaction() {
        return $this->belongsTo('App\Transaction');
    }
}
