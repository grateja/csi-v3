<?php

namespace App;

use App\Jobs\AutoSynch;
use App\Traits\UsesSynch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UsesUuid;

class PartialPayment extends Model
{
    use SoftDeletes, UsesUuid, UsesSynch;


    protected $fillable = [
        'id', 'transaction_id', 'cash', 'points', 'discount', 'total_amount', 'change', 'user_id', 'paid_to', 'points_in_peso', 'balance', 'total_cash', 'card_load_used', 'rfid', 'synched',
    ];

    public $appends = [
        'total_paid'
    ];

    public function getTotalPaidAttribute() {
        // return (float)$this->total_amount - (float)$this->points_in_peso - (float)$this->rfid - (float)$this->card_load_used - (float)$this->cash;
        return (float)$this->total_amount - (float)$this->balance;
    }

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function transaction() {
        return $this->belongsTo('App\Transaction');
    }

    public function queSynch() {
        return (new AutoSynch('transaction_payments', $this->id))->delay(5);
    }
}
