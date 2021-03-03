<?php

namespace App;

use App\Jobs\AutoSynch;
use App\Traits\UsesSynch;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WashingService extends Model
{
    use SoftDeletes, UsesUuid, UsesSynch;

    protected $fillable = [
        'name', 'description', 'img_path', 'price', 'machine_type', 'regular_minutes', 'additional_minutes', 'points', 'synched', 'deleted_at', 'updated_at',
    ];

    public function fullServiceItems() {
        return $this->hasMany('App\FullServiceItem');
    }

    public function serviceTransactionItems() {
        return $this->hasMany('App\ServiceTransactionItem');
    }

    protected static function boot() {
        static::deleting(function($model) {
            $model->fullServiceItems()->delete();
        });

        parent::boot();
    }

    public function queSynch() {
        return (new AutoSynch('washing_services', $this->id))->delay(5);
    }
}
