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
        // 'remaining_time',
        // 'total_cycle_count'
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

    public function remoteActivate($pulse) {
        $url = "$this->ip_address/activate?pulse=$pulse";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_TIMEOUT, 5);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

    public function tapActivate($customerName) {
        if($this->is_running) {
            $minutes = $this->total_minutes + $this->additional_time;
            $price = $this->additional_price;
            $timeActivated = $this->time_activated;

            if($this->machine_type[1] == 'd') {
                // this is dryer
                if($this->total_minutes + $this->additional_time > 100) {
                    return false;
                }
            } else if($this->machine_type[1] == 'w') {
                // this is washer
                if($this->total_minutes >= $this->initial_time + $this->additional_time) {
                    return false;
                }
            }

            $machineUsage = MachineUsage::where('machine_id', $this->id)->orderByDesc('updated_at')->first();
            $machineUsage->update([
                'minutes' => $minutes,
            ]);

        } else {
            $minutes = $this->initial_time;
            $price = $this->initial_price;
            $timeActivated = Carbon::now();

            $machineUsage = MachineUsage::create([
                'machine_id' => $this->id,
                'customer_name' => $customerName,
                'minutes' => $minutes,
            ]);
        }

        $this->update([
            'time_activated' => $timeActivated,
            'total_minutes' => $minutes,
            'customer_name' => $customerName,
        ]);

        $this['price'] = $price;
        return $this;
    }
}
