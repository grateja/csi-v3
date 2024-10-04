<?php

namespace App;

use App\Jobs\AutoSynch;
use App\Traits\UsesSynch;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

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
        return (new AutoSynch('lagoon_transaction_items', $this->id))->delay(0);
    }

    protected static function boot() {
        static::deleting(function($model) {
            $monitorChecker = MonitorChecker::firstOrCreate(['id' => 'default']);
            $monitorChecker->update([
                'transaction_id' => $model->transaction_id,
                'token' => $model->id,
                'action' => 'remove-lagoon',
            ]);
        });
        static::created(function($model) {
            $monitorChecker = MonitorChecker::firstOrCreate(['id' => 'default']);
            $monitorChecker->update([
                'transaction_id' => $model->transaction_id,
                'token' => $model->id,
                'action' => 'add-lagoon',
            ]);
        });
        parent::boot();
    }
}
