<?php

namespace App;

use App\Jobs\AutoSynch;
use App\Traits\UsesSynch;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PerKiloWashDry extends Model
{
    use SoftDeletes, UsesUuid, UsesSynch;

    protected $fillable = [
        'name',
        'delicate_price',
        'warm_price',
        'hot_price',
        'superwash_price',
    ];

    public function queSynch() {
        return (new AutoSynch('other_services', $this->id))->delay(5);
    }
}
