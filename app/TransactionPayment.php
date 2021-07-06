<?php

namespace App;

use App\Jobs\AutoSynch;
use App\Traits\UsesSynch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UsesUuid;

class TransactionPayment extends Model
{
    use SoftDeletes, UsesUuid, UsesSynch;

    // protected $primaryKey = 'transaction_id';

    public $appends = [
        'changeStr', 'discount_in_peso'
    ];

    protected $fillable = [
        'id', 'cash', 'date', 'points', 'discount', 'discount_name', 'total_amount', 'change', 'user_id', 'paid_to', 'points_in_peso', 'balance', 'total_cash', 'card_load_used', 'rfid', 'synched',
    ];

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function transaction() {
        return $this->belongsTo('App\Transaction', 'id', 'id');
    }

    public function getChangeStrAttribute() {
        return 'P' . number_format($this->change, 2);
    }

    public function getDiscountInPesoAttribute() {
        return $this->total_amount * $this->discount / 100;
    }

    public function queSynch() {
        return (new AutoSynch('transaction_payments', $this->id))->delay(5);
    }
}
