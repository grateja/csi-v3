<?php

namespace App;

use App\Jobs\AutoSynch;
use App\Traits\UsesSynch;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OtherService extends Model
{
    use SoftDeletes, UsesUuid, UsesSynch;

    protected $fillable = [
        'name', 'description', 'img_path', 'price', 'points',
    ];

    public function fullServiceItems() {
        return $this->hasMany('App\FullServiceItem');
    }

    protected static function boot() {
        static::deleting(function($model) {
            $model->fullServiceItems()->delete();
        });

        parent::boot();
    }

    public function queSynch() {
        return (new AutoSynch('other_services', $this->id))->delay(5);
    }
}
