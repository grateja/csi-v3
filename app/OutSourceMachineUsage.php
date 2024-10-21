<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Jobs\AutoSynch;
use App\Traits\UsesSynch;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\SoftDeletes;

class OutSourceMachineUsage extends Model
{
    use SoftDeletes, UsesUuid, UsesSynch;

    protected $fillable = [
        'machine_id',
        'elux_machine_id',
        'user_id',
        'out_source_id',
        'out_source_service_id',
        'minutes',
    ];

    public function outSource() {
        return $this->belongsTo('App\OutSource');
    }

    public function outSourceService() {
        return $this->belongsTo('App\OutSourceService');
    }

    public function eluxMachine() {
        return $this->belongsTo('App\EluxMachine');
    }

    public function machine() {
        return $this->belongsTo('App\Machine');
    }
}
