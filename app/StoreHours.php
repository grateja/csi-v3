<?php

namespace App;

use App\Jobs\AutoSynch;
use App\Traits\UsesSynch;
use App\Traits\UsesUuid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class StoreHours extends Model
{
    use UsesUuid, UsesSynch;

    protected $fillable = [
        'id', 'day_index', 'opens_at', 'closes_at', 'synched', 'updated_at', 'created_at',
    ];

    public function queSynch() {
        return (new AutoSynch('rfid_cards', $this->id))->delay(5);
    }

    public $appends = [
        'today',
        'day',
        'display',
    ];

    public function getTodayAttribute() {
        // returns if this is today or not
        return Carbon::now()->dayOfWeek == $this->day_index;
    }

    public function getDayAttribute() {
        switch($this->day_index) {
            case 1:
                return 'Monday';
            case 2:
                return 'Tuesday';
            case 3:
                return 'Wednesday';
            case 4:
                return 'Thursday';
            case 5:
                return 'Friday';
            case 6:
                return 'Saturday';
            case 7:
                return 'Sunday';
            default:
                return '';
        }
    }

    public function getDisplayAttribute() {
        return "$this->opens_at - $this->closes_at";
    }
}
