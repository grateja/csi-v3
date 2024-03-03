<?php

namespace App;

use App\Jobs\AutoSynch;
use App\Traits\UsesSynch;
use App\Traits\UsesUuid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EluxMachine extends Model
{
    use SoftDeletes, UsesUuid, UsesSynch;

    public $appends = [
        'time_ends_in',
        'is_running',
        // 'remaining_time',
        // 'total_cycle_count'
    ];

    protected $fillable = [
        'id', 'machine_type', 'machine_name', 'model', 'ip_address', 'stack_order', 'customer_name', 'time_activated', 'total_minutes',
    ];

    public function getTimeEndsInAttribute() {
        if($this->time_activated == null) {
            return false;
        }
        $t = new Carbon($this->time_activated);
        return $t->addMinutes($this->total_minutes);
    }

    public function getIsRunningAttribute() {
        return $this->time_ends_in > Carbon::now();
    }

    public function customer() {
        return $this->belongsTo('App\Customer');
    }

    public function queSynch() {
        return (new AutoSynch('elux_machines', $this->id))->delay(5);
    }


}
