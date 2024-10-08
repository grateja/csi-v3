<?php

namespace App;

use App\Jobs\AutoSynch;
use App\Traits\UsesSynch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UsesUuid;

class RfidLoadTransaction extends Model
{
    use SoftDeletes, UsesUuid, UsesSynch;

    protected $fillable = [
        'customer_name', 'rfid', 'rfid_card_id', 'amount', 'remaining_balance', 'current_balance', 'staff_name', 'remarks', 'cash', 'change', 'synched',
    ];

    public static function filterKeys($val) {
        $keys = [
            'customer_name' => 'Customer name',
            'rfid' => 'RFID',
            'created_at' => 'Date',
            'amount' => 'Load amount',
            'default' => 'created_at',
        ];

        $filter = collect($keys)->filter(function($value) use ($val, $keys) {
            return strcasecmp($value, $val) == 0 && $keys['default'] != $val;
        })->keys();
        return $filter->first() ?? $keys['default'];
    }

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function rfidCard() {
        return $this->belongsTo('App\RfidCard');
    }

    public function getDateTimeStrAttribute() {
        return $this->created_at->format('M-d, Y H:i A');
    }

    public function queSynch() {
        return (new AutoSynch('rfid_load_transactions', $this->id))->delay(0);
    }
}
