<?php

namespace App;

use App\Jobs\AutoSynch;
use App\Traits\UsesSynch;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScarpaVariation extends Model
{
    use SoftDeletes, UsesUuid, UsesSynch;

    protected $fillable = [
        'scarpa_category_id', 'action', 'selling_price', 'id', 'created_at', 'updated_at', 'synch',
    ];

    public function scarpaCategory() {
        return $this->belongsTo('App\ScarpaCategory');
    }

    public function queSynch() {
        return (new AutoSynch('scarpa_variations', $this->id))->delay(0);
    }
}
