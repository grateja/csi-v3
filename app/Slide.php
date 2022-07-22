<?php

namespace App;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;

class Slide extends Model
{
    use UsesUuid;

    protected $fillable = [
        'id', 'source', 'order', 'caption', 'description', 'event_id'
    ];

    public function event() {
        return $this->belongsTo('App\Event');
    }
}
