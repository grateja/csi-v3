<?php

namespace App;

use App\Traits\UsesSynch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UsesUuid;
use App\Jobs\AutoSynch;

class EluxService extends Model
{
    use SoftDeletes, UsesUuid, UsesSynch;

    protected $fillable = [
        'id', 'service_type', 'name', 'price', 'pulse_count', 'model', 'minutes',
    ];

    public function eluxServiceTransactionItems() {
        return $this->hasMany('App\EluxServiceTransactionItem');
    }

    public function queSynch() {
        return (new AutoSynch('elux_services', $this->id))->delay(0);
    }
}
