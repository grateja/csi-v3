<?php

namespace App;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MachineRemarks extends Model
{
    use SoftDeletes, UsesUuid;

    protected $fillable = [
        'title', 'remarks', 'user_id', 'remaining_time', 'machine_id',
    ];
}
