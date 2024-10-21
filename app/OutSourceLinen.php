<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Jobs\AutoSynch;
use App\Traits\UsesSynch;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\SoftDeletes;

class OutSourceLinen extends Model
{
    use SoftDeletes, UsesUuid, UsesSynch;

    protected $fillable = [
        'out_source_id',
        'category',
        'name',
        'regular_price',
        'with_stain_light',
        'with_stain_medium',
        'with_stain_heavy',
        'dry_weight',
    ];
}

