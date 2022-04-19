<?php

namespace App;

use App\Jobs\AutoSynch;
use App\Traits\UsesSynch;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LagoonTransactionItem extends Model
{
    use SoftDeletes, UsesUuid, UsesSynch;
    protected $fillable = [
        'transaction_id', 'name', 'price', 'lagoon_id', 'saved', 'synched',
    ];

    public function transaction() {
        return $this->belongsTo('App\Transaction');
    }

    public function Lagoon() {
        return $this->belongsTo('App\Lagoon');
    }

    public function queSynch() {
        return (new AutoSynch('lagoon_transaction_items', $this->id))->delay(5);
    }
}
