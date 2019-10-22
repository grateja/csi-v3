<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use App\Traits\UsesUuid;

class Machine extends Model
{
    use SoftDeletes, UsesUuid;

    protected $fillable = [
        'ip_address',
        'machine_type',
        'total_minutes',
        'time_activated',
        'machine_name',
        'synched',
        'created_at',
        'updated_at',
        'deleted_at',
        'initial_time',
        'additional_time',
        'initial_price',
        'additional_price',
        'customer_name',
    ];

    public $appends = [
        'time_ends_in',
        'is_running',
        'remaining_time',
        'total_cycle_count'
    ];

    public function getTimeEndsInAttribute() {
        $t = new Carbon($this->time_activated);
        return $t->addMinutes($this->total_minutes);
    }

    public function getIsRunningAttribute() {
        return $this->time_ends_in > Carbon::now();
    }

    public function customer() {
        if($this->is_running) {
            return $this->customer_name;
        } else {
            return 'Last customer: ' . $this->customer_name;
        }
    }

    public function getRemainingTimeAttribute() {
        if($this->isRunning) {
            $t = new Carbon($this->time_activated);
            $t->addMinutes($this->total_minutes + 1);
            return $t->diffInMinutes() . ' minute(s) remaining';
        } else {
            return 'Iddle';
        }
    }
}
