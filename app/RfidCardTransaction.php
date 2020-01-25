<?php

namespace App;

use App\Traits\UsesSynch;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RfidCardTransaction extends Model
{
    use SoftDeletes, UsesUuid, UsesSynch;

    protected $fillable = [
        'rfid', 'machine_name', 'owner_name', 'price', 'minutes', 'synched', 'machine_id', 'rfid_card_id',
    ];
}
