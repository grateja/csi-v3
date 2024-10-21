<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Jobs\AutoSynch;
use App\Traits\UsesSynch;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\SoftDeletes;

class OutSourceService extends Model
{
    use SoftDeletes, UsesUuid, UsesSynch;

    protected $fillable = [
        'name',
        'description',
        'pulse_count',
        'minutes',
    ];
}
