<?php

namespace App;

use App\Jobs\AutoSynch;
use App\Traits\UsesSynch;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MachineRemarks extends Model
{
    use SoftDeletes, UsesUuid, UsesSynch;

    protected $fillable = [
        'title', 'remarks', 'user_id', 'remaining_time', 'machine_id', 'synched',
    ];

    public function queSynch() {
        return (new AutoSynch('machine_remarks', $this->id))->delay(5);
    }

    public function machine() {
        return $this->belongsTo('App\Machine');
    }

    public function user() {
        return $this->belongsTo('App\User');
    }
}
