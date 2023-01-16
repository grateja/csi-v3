<?php

namespace App;

use App\Jobs\AutoSynch;
use App\Traits\UsesSynch;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;

class MachineUsage extends Model
{
    use UsesUuid, UsesSynch, UsesSynch;

    protected $fillable = [
        'id', 'machine_id', 'customer_name', 'minutes', 'activation_type', 'remarks', 'synched', 'price',
    ];

    public function queSynch() {
        return (new AutoSynch('machine_usages', $this->id))->delay(5);
    }
}
