<?php

namespace App;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;

class SysDefault extends Model
{
    use UsesUuid;

    protected $fillable = [
        'event_id', 'announcement_id',
    ];

    public function announcement() {
        return $this->belongsTo('App\Announcement');
    }

    public function event() {
        return $this->belongsTo('App\Event');
    }
}
