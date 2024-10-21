<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Jobs\AutoSynch;
use App\Traits\UsesSynch;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\SoftDeletes;

class OutSourceJobOrder extends Model
{
    use SoftDeletes, UsesUuid, UsesSynch;

    protected $fillable = [
        'out_source_id',
        'job_order_number',
        'user_id',
        'out_source_statement_of_account_id'
    ];

    public function outSourceJobOrderLinens() {
        return $this->hasMany('App\OutSourceJobOrderLinen');
    }

    public function user() {
        return $this->belongsTo('App\User');
    }

    public static function resultWithTotal($outSourceId)
    {
        return self::where('out_source_job_orders.out_source_id', $outSourceId)
            ->leftJoin('out_source_job_order_linens', 'out_source_job_orders.id', '=', 'out_source_job_order_linens.out_source_job_order_id')
            ->select(
                'out_source_job_orders.id',
                'out_source_job_orders.job_order_number',
                'out_source_job_orders.out_source_id',
                'out_source_job_orders.created_at',
                'out_source_job_order_linens.out_source_job_order_id'
            )
            ->selectRaw('SUM(out_source_job_order_linens.unit_price * out_source_job_order_linens.quantity) as total')
            ->groupBy(
                'out_source_job_orders.id',
                'out_source_job_orders.job_order_number',
                'out_source_job_orders.out_source_id',
                'out_source_job_orders.created_at',
                'out_source_job_order_linens.out_source_job_order_id'
            );
    }
}
