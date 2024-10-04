<?php

namespace App;

use App\Jobs\AutoSynch;
use App\Traits\UsesSynch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UsesUuid;

class Product extends Model
{
    use SoftDeletes, UsesUuid, UsesSynch;

    protected $fillable = [
        'name', 'description', 'minimum_stock', 'current_stock', 'img_path', 'selling_price', 'synched', 'deleted_at',
    ];

    public function fullServiceProducts() {
        return $this->hasMany('App\FullServiceProduct');
    }

    public function productTransactionItems() {
        return $this->hasMany('App\ProductTransactionItem');
    }

    protected static function boot() {
        static::deleting(function($model) {
            $model->fullServiceProducts()->delete();

            $model->productTransactionItems()->where('saved', false)->delete();
        });

        parent::boot();
    }

    public function queSynch() {
        return (new AutoSynch('products', $this->id))->delay(0);
    }
}
