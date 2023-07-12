<?php

namespace App;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use UsesUuid;

    protected $fillable = [
        'id', 'title', 'description', 'date_from', 'date_until', 'event_type_id', 'updated_at'
    ];

    public $appends = [
        'is_default',
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

    public function audio() {
        return $this->hasOne('App\Audio');
    }

    public function getIsDefaultAttribute() {
        return $this->sysDefault != null;
    }

    public function sysDefault() {
        return $this->hasOne('App\SysDefault');
    }
}
