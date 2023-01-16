<?php

namespace App;

// use App\Jobs\AutoSynch;
// use App\Traits\UsesSynch;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lagoon extends Model
{
    use SoftDeletes, UsesUuid;//, UsesSynch;

    public $timestamps = false;

    protected $fillable = [
        'id',
        'name',
        'category',
        'img_path',
        'price',
        'synched'
    ];

    // public function queSynch() {
    //     return (new AutoSynch('lagoons', $this->id))->delay(5);
    // }
}
