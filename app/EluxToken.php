<?php

namespace App;

use App\Jobs\AutoSynch;
use App\Traits\UsesSynch;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EluxToken extends Model
{
    use SoftDeletes, UsesUuid, UsesSynch;

    protected $fillable = [
        'id', 'customer_id', 'elux_machine_id', 'elux_service_transaction_item_id', 'service_type', 'name', 'price', 'pulse_count', 'model', 'minutes', 'used'
    ];

    public function transaction() {
        return $this->belongsTo('App\Transaction');
    }

    public function eluxMachine() {
        return $this->belongsTo('App\EluxMachine');
    }

    public function machineName() {
        if($machine = $this->eluxMachine) {
            return $machine->machineName;
        }
        return "";
    }

    public function queSynch() {
        return (new AutoSynch('elux_tokens', $this->id))->delay(0);
    }
}
