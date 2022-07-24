<?php

namespace App;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;

class Slide extends Model
{
    use UsesUuid;
    public $timestamps = false;

    public function event() {
        return $this->belongsTo('App\Event');
    }
}
