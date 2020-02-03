<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UsesUuid;

class RfidLoadTransaction extends Model
{
    use SoftDeletes, UsesUuid;

    protected $fillable = [
        'customer_name', 'rfid', 'rfid_card_id', 'amount', 'remaining_balance', 'current_balance', 'user_id', 'remarks', 'cash', 'change'
    ];

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function rfidCard() {
        return $this->belongsTo('App\RfidCard');
    }
}
