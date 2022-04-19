<?php

namespace App;

use App\Jobs\AutoSynch;
use App\Traits\UsesSynch;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScarpaCleaningTransactionItem extends Model
{
    use SoftDeletes, UsesUuid, UsesSynch;

    protected $fillable = [
        'transaction_id', 'scarpa_category_id', 'scarpa_variation_id', 'name', 'price', 'saved',
    ];

    public function transaction() {
        return $this->belongsTo('App\Transaction');
    }

    public function queSynch() {
        return (new AutoSynch('scarpa_cleaning_transaction_items', $this->id))->delay(5);
    }
}
