<?php

namespace App;

use App\Jobs\AutoSynch;
use App\Traits\UsesSynch;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScarpaCategory extends Model
{
    use SoftDeletes, UsesUuid, UsesSynch;

    public $timestamps = false;

    protected $fillable = [
        'name', 'description', 'img_path', 'saved',
    ];

    public function scarpaVariations() {
        return $this->hasMany('App\ScarpaVariation');
    }

    public function scarpaCleaningTransactionItems() {
        return $this->hasMany('App\ScarpaCleaningTransactionItem');
    }

    public function queSynch() {
        return (new AutoSynch('scarpa_categories', $this->id))->delay(0);
    }
}
