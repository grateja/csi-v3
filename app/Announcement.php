<?php

namespace App;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use UsesUuid;

    protected $fillable = [
        'content', 'date',
    ];
}
