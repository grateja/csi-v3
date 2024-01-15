<?php

namespace App;

use App\Jobs\AutoSynch;
use App\Traits\UsesSynch;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EluxMachine extends Model
{
    use SoftDeletes, UsesUuid, UsesSynch;

    protected $fillable = [
        'id', 'machine_type', 'machine_name', 'model', 'ip_address', 'stack_order',
    ];

    public function queSynch() {
        return (new AutoSynch('elux_machines', $this->id))->delay(5);
    }
}
