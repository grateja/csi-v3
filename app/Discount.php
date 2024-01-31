<?php

namespace App;

use App\Traits\UsesSynch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UsesUuid;
use App\Jobs\AutoSynch;

class Discount extends Model
{
    use SoftDeletes, UsesUuid, UsesSynch;

    protected $fillable = [
        'id', 'name', 'percentage', 'deleted_at',
    ];

    public function queSynch() {
        return (new AutoSynch('discounts', $this->id))->delay(5);
    }
}
