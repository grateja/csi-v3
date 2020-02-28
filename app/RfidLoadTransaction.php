<?php

namespace App;

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

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function rfidCard() {
        return $this->belongsTo('App\RfidCard');
    }

    public function getDateTimeStrAttribute() {
        return $this->created_at->format('M-d, Y H:i A');
    }
}
