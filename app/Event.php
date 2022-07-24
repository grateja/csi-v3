<?php

namespace App;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use UsesUuid;

    protected $fillable = [
        'id', 'title', 'description', 'date', 'event_type_id', 'updated_at'
    ];

    public function slides() {
        return $this->hasMany('App\Slide');
    }

    public function eventType() {
        return $this->belongsTo('App\EventType');
    }

    public function video() {
        return $this->hasOne('App\Video');
    }
}
