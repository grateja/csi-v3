<?php

namespace App;

use App\Traits\UsesSynch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UsesUuid;

class Product extends Model
{
    use SoftDeletes, UsesUuid, UsesSynch;

    protected $fillable = [
        'name', 'description', 'minimum_stock', 'current_stock', 'img_path', 'selling_price',
    ];

    public function fullServiceProducts() {
        return $this->hasMany('App\FullServiceProduct');
    }

    protected static function boot() {
        static::deleting(function($model) {
            $model->fullServiceProducts()->delete();
        });

        parent::boot();
    }
}
