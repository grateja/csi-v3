<?php

namespace App;

use App\Jobs\AutoSynch;
use App\Traits\UsesSynch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use App\Traits\UsesUuid;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Machine extends Model
{
    use SoftDeletes, UsesUuid, UsesSynch;

    protected $fillable = [
        'stack_order',
        'ip_address',
        'mac_address',
        'machine_type',
        'total_minutes',
        'time_activated',
        'machine_name',
        'minutes',
        'synched',
        'created_at',
        'updated_at',
        'deleted_at',
        'initial_time',
        'additional_time',
        'initial_price',
        'additional_price',
        'remarks',
        'user_name',
        'customer_id',
        'customer_wash_id',
        'customer_dry_id',
        'synched',
    ];

    public $appends = [
        'time_ends_in',
        'is_running',
        // 'remaining_time',
        // 'total_cycle_count'
    ];

    public function machineUsages() {
        return $this->hasMany('App\MachineUsage');
    }

    public function outSourceMachineUsage() {
        return $this->hasMany('App\OutSourceMachineUsage');
    }

    public function totalUsage() {
        return $this->machineUsages();
    }

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

    public function getRemainingTimeAttribute() {
        if($this->isRunning) {
            $t = new Carbon($this->time_activated);
            $t->addMinutes($this->total_minutes + 1);
            return $t->diffInMinutes() . ' minute(s) remaining';
        } else {
            return 'Iddle';
        }
    }

    public function remainingTime() {
        if($this->isRunning) {
            $t = new Carbon($this->time_activated);
            $t->addMinutes($this->total_minutes + 1);
            return $t->diffInMinutes();
        } else {
            return 0;
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

    public function tapActivate($rfidCard, $tapToken = null) {
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
                'id' => $tapToken ? $tapToken : $machineUsage->id,
                'minutes' => $minutes,
                'price' => DB::raw('price+' . $price),
            ]);

            $rfidCardTransaction = RfidCardTransaction::where([
                'machine_id' => $this->id,
                'rfid_card_id' => $rfidCard->id,
            ])->orderByDesc('updated_at')->first();

            if($rfidCardTransaction == null) {
                return false;
            }

            $rfidCardTransaction->update([
                'price' => DB::raw('price+' . $price),
                'minutes' => $minutes,
            ]);

        } else {
            $minutes = $this->initial_time;
            $price = $this->initial_price;
            $timeActivated = Carbon::now();

            $machineUsage = MachineUsage::create([
                'id' => $tapToken,// ? $tapToken : Str::uuid(),
                'machine_id' => $this->id,
                'customer_name' => $rfidCard->owner_name,
                'minutes' => $minutes,
                'activation_type' => 'card',
                'price' => $price,
            ]);

            $rfidCardTransaction = RfidCardTransaction::create([
                'rfid' => $rfidCard->rfid,
                'machine_name' => $this->machine_name,
                'owner_name' => $rfidCard->owner_name,
                'price' => $price,
                'minutes' => $minutes,
                'machine_id' => $this->id,
                'rfid_card_id' => $rfidCard->id,
                'card_type' => $rfidCard->card_type,
            ]);
        }

        $this->update([
            'time_activated' => $timeActivated,
            'total_minutes' => $minutes,
            'user_name' => $rfidCard->owner_name,
            'remarks' => 'Activated by card: ' . $rfidCard->owner_name,
            'customer_wash_id' => null,
            'customer_dry_id' => null,
        ]);

        $this['price'] = $price;
        $this['minutes'] = $minutes;
        $this['machineUsage'] = $machineUsage;
        $this['rfidCardTransaction'] = $rfidCardTransaction;
        return $this;
    }

    public function queSynch() {
        return (new AutoSynch('machines', $this->id))->delay(0);
    }
}
