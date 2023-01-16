<?php

namespace App;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;

class UnregisteredCard extends Model
{
    use UsesUuid;
    protected $fillable = [
        'rfid', 'machine_name', 'updated_at',
    ];
}
