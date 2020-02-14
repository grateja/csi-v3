<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UsesUuid;

class TransactionPayment extends Model
{
    use SoftDeletes, UsesUuid;

    public $timestamps = false;

    protected $primaryKey = 'transaction_id';

    public $appends = [
        'changeStr', 'discount_in_peso'
    ];

    protected $fillable = [
        'transaction_id', 'cash', 'points', 'discount', 'total_amount', 'change', 'user_id', 'paid_to', 'points_in_peso', 'balance', 'total_cash', 'card_load_used', 'rfid',
    ];

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function transaction() {
        return $this->belongsTo('App\Transaction');
    }

    public function getChangeStrAttribute() {
        return 'P' . number_format($this->change, 2);
    }

    public function getDiscountInPesoAttribute() {
        return $this->total_amount * $this->discount / 100;
    }
}
