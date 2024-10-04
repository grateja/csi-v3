<?php

namespace App;

use App\Jobs\AutoSynch;
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

    protected static function boot() {
        static::deleting(function($model) {
            // $monitorChecker = MonitorChecker::firstOrCreate(['id' => 'default']);
            // $monitorChecker->update([
            //     'transaction_id' => $model->transaction_id,
            //     'token' => $model->id,
            //     'action' => 'remove-product',
            // ]);
        });
        static::created(function($model) {
            // $monitorChecker = MonitorChecker::firstOrCreate(['id' => 'default']);
            // $monitorChecker->update([
            //     'transaction_id' => $model->transaction_id,
            //     'token' => $model->id,
            //     'action' => 'add-product',
            // ]);
        });
        parent::boot();
    }

    public function queSynch() {
        return (new AutoSynch('product_transaction_items', $this->id))->delay(0);
    }
}
