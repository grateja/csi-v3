<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Jobs\AutoSynch;
use App\Traits\UsesSynch;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\SoftDeletes;

class OutSourceStatementOfAccount extends Model
{
    use SoftDeletes, UsesUuid, UsesSynch;

    protected $fillable = [
        'out_source_id',
        'soa_number',
        'remarks',
        'vat',
        'total_amount',
    ];

    public function outSourceJobOrders() {
        return $this->hasMany('App\OutSourceJobOrder');
    }
}
