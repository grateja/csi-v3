<?php

namespace App;

use App\Jobs\AutoSynch;
use App\Traits\UsesSynch;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;

class EluxMachineUsage extends Model
{
    use UsesUuid, UsesSynch, UsesSynch;

    protected $fillable = [
        'id', 'elux_machine_id', 'customer_name', 'minutes', 'remarks', 'synched', 'price',
    ];

    public function queSynch() {
        return (new AutoSynch('elux_machine_usages', $this->id))->delay(5);
    }
}
