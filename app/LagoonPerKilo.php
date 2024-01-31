<?php

namespace App;

use App\Traits\UsesSynch;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LagoonPerKilo extends Model
{
    use SoftDeletes, UsesUuid, UsesSynch;

    public $timestamps = false;

    protected $fillable = [
        'id',
        'name',
        'price_per_kilo',
    ];

    public function queSynch() {
        return (new AutoSynch('lagoon_per_kilos', $this->id))->delay(5);
    }
}
