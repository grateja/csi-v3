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
        'name', 'description', 'img_path', 'price', 'machine_type', 'regular_minutes','quick_minutes', 'more_rinse_minutes', 'premium_minutes', 'additional_minutes', 'points', 'synched', 'deleted_at', 'updated_at',
    ];

    public $appends = [
        'minutes',
        'pulse_count',
    ];

    public function fullServiceItems() {
        return $this->hasMany('App\FullServiceItem');
    }

    public function serviceTransactionItems() {
        return $this->hasMany('App\ServiceTransactionItem');
    }

    public function getMinutesAttribute() {
        return $this->regular_minutes + $this->quick_minutes + $this->more_rinse_minutes + $this->premium_minutes + $this->additional_minutes;
    }

    public function getPulseCountAttribute() {
        if(env('MACHINE_ACTIVATION_METHOD', 'els') == 'els') {
            // default els
            if($this->regular_minutes > 0) {
                return 1;
            } else if($this->additional_minutes > 0) {
                return 2;
            }
        } else {
            // nsfot
            if($this->regular_minutes > 0) {
                return 2;
            } else if($this->quick_minutes > 0) {
                return 1;
            } else if($this->more_rinse_minutes > 0) {
                return 2;
            } else if($this->premium_minutes > 0) {
                return 3;
            } else if($this->additional_minutes > 0) {
                return 4;
            }
        }
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
