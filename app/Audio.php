<?php

namespace App;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;

class Audio extends Model
{
    use UsesUuid;

    protected $fillable = [
        'id', 'event_id', 'source',
    ];
}
