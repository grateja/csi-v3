<?php

namespace App;

use App\Jobs\AutoSynch;
use App\Traits\UsesSynch;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EluxServiceTransactionItem extends Model
{
    use SoftDeletes, UsesUuid, UsesSynch;

    protected $fillable = [
        'id', 'transaction_id', 'elux_service_id', 'service_type', 'name', 'price', 'pulse_count', 'model', 'minutes', 'saved'
    ];

    public function transaction() {
        return $this->belongsTo('App\Transaction');
    }

    public function eluxService() {
        return $this->belongsTo('App\EluxService');
    }

    public function eluxToken() {
        return $this->hasOne('App\EluxToken');
    }

    public function machineName() {
        if($token = $this->eluxToken) {
            if($machine = $token->eluxMachine) {
                return $machine->name;
            }
        }
        return null;
    }

    public function used() {
        if($token = $this->eluxToken) {
            return $token->used;
        }
    }

    public function queSynch() {
        return (new AutoSynch('elux_service_transaction_items', $this->id))->delay(5);
    }
}
