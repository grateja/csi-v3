<?php

namespace App;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use UsesUuid;

    protected $fillable = [
        'content', 'date_from', 'date_until', 'marquee_on',
    ];

    public $appends = [
        'is_default',
    ];

    public function getIsDefaultAttribute() {
        return $this->sysDefault != null;
    }

    public function sysDefault() {
        return $this->hasOne('App\SysDefault');
    }
}
