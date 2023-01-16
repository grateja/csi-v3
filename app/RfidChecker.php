<?php

namespace App;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use App\Jobs\ClearRFIDChecker;

class RfidChecker extends Model
{
    use UsesUuid;

    protected $fillable = [
        'rfid'
    ];
}
