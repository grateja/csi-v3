<?php

namespace App;

use App\Jobs\AutoSynch;
use App\Traits\UsesSynch;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class RfidCardTransaction extends Model
{
    use SoftDeletes, UsesUuid, UsesSynch;

    protected $fillable = [
        'rfid', 'machine_name', 'owner_name', 'price', 'minutes', 'synched', 'machine_id', 'rfid_card_id', 'card_type',
    ];

    public static function filterKeys($val) {
        $keys = [
            'owner_name' => 'Customer name',
            'rfid' => 'RFID',
            'created_at' => 'Date',
            'machine_name' => 'Machine name',
            'default' => 'created_at',
        ];

        $filter = collect($keys)->filter(function($value) use ($val, $keys) {
            return strcasecmp($value, $val) == 0 && $keys['default'] != $val;
        })->keys();
        return $filter->first() ?? $keys['default'];
    }

    public function getDateTimeStrAttribute() {
        return $this->created_at->format('M-d, Y H:i A');
    }

    public function queSynch() {
        return (new AutoSynch('rfid_card_transactions', $this->id))->delay(0);
    }
}
