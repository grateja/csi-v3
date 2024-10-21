<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Jobs\AutoSynch;
use App\Traits\UsesSynch;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\SoftDeletes;

class OutSource extends Model
{
    use SoftDeletes, UsesUuid, UsesSynch;

    protected $fillable = [
        'abbr',
        'company_name',
        'address',
    ];

    public function linens() {
        return $this->hasMany('App\OutSourceLinen');
    }
}
