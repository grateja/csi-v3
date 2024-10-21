<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Jobs\AutoSynch;
use App\Traits\UsesSynch;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\SoftDeletes;

class OutSourceMachineUsageLinen extends Model
{
    use SoftDeletes, UsesUuid, UsesSynch;

    protected $fillable = [
        'out_source_machine_usage_id',
        'name',
        'quantity'
    ];
}
