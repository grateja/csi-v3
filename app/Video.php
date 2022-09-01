<?php

namespace App;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use UsesUuid;

    protected $fillable = [
        'id', 'event_id', 'source', 'title', 'description', 'order'
    ];

    public function event() {
        return $this->belongsTo('App\Event');
    }
}
