<?php

namespace App;

use App\Jobs\AutoSynch;
use App\Traits\UsesSynch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UsesUuid;

class ProductPurchase extends Model
{
    use SoftDeletes, UsesUuid, UsesSynch;

    protected $fillable = [
        'date', 'product_id', 'product_name', 'receipt', 'quantity', 'unit_cost', 'remarks', 'staff_name', 'synched',
    ];

    public static function filterKeys($val) {
        $keys = [
            'product_name' => 'Product name',
            'date' => 'Date',
            'remarks' => 'Remarks',
            'receipt' => 'Receipt',
            'staff_name' => 'Encoded by',
            'default' => 'date',
        ];

        $filter = collect($keys)->filter(function($value) use ($val, $keys) {
            return strcasecmp($value, $val) == 0 && $keys['default'] != $val;
        })->keys();
        return $filter->first() ?? $keys['default'];
    }

    public function product() {
        return $this->belongsTo('App\Product');
    }

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function queSynch() {
        return (new AutoSynch('product_purchases', $this->id))->delay(5);
    }
}
