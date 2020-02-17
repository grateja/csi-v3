<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UsesUuid;
use Carbon\Carbon;

class RfidCard extends Model
{
    use SoftDeletes, UsesUuid;

    public $appends = [
        'owner_name',
    ];

    protected $fillable = [
        'rfid', 'balance', 'customer_id', 'user_id', 'card_type', 'unlimited',
    ];

    public function customer() {
        return $this->belongsTo('App\Customer');
    }

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function getOwnerNameAttribute() {
        if($this->card_type == 'c' && $this->customer) {
            return $this->customer->name;
        } else if($this->card_type == 'u' && $this->user) {
            return $this->user->name;
        }
    }

    public function ownerId() {
        if($this->card_type == 'c' && $this->customer) {
            return $this->customer_id;
        } else if($this->card_type == 'u' && $this->user) {
            return $this->user_id;
        }
    }

    protected static function boot() {
        static::deleting(function($model) {
            $model->update([
                'rfid' => $model->rfid . '[' . Carbon::now()->toDateTimeString() . ']',
            ]);
        });
        parent::boot();
    }
}
