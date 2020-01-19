<?php

namespace App;

use App\Traits\UsesSynch;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;

class MachineUsage extends Model
{
    use UsesUuid, UsesSynch;

    protected $fillable = [
        'machine_id', 'customer_name', 'minutes', 'activation_type', 'synched',
    ];
}
