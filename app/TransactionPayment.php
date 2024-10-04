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
        'changeStr', 'discount_in_peso', 'payment_method', 'collected', 'discounted_price',
    ];

    protected $fillable = [
        'id',
        'cash',
        'or_number',
        'date',
        'points',
        'discount',
        'discount_name',
        'cash_less_provider',
        'cash_less_reference_number',
        'cash_less_amount',
        'total_amount',
        'change',
        'user_id',
        'paid_to',
        'points_in_peso',
        'balance',
        'total_cash',
        'card_load_used',
        'rfid',
        'synched',
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

    public function getCollectedAttribute() {
        // return 'fuck you';
        return $this->total_amount - $this->getDiscountInPesoAttribute() - $this->points_in_peso - $this->cash_less_amount;
    }

    public function getDiscountedPriceAttribute() {
        if($this->discount) {
            return $this->total_amount - $this->discount_in_peso;
        }
        return $this->total_amount;
    }

    public function getPaymentMethodAttribute() {
        $paymentMethod = [];
        if($this->cash) {
            $paymentMethod[] = 'cash';
        }
        if($this->cash_less_provider) {
            $paymentMethod[] = $this->cash_less_provider;
        }
        if($this->points_in_peso) {
            $paymentMethod[] = 'points';
        }

        return implode(',', $paymentMethod);
    }

    public function queSynch() {
        return (new AutoSynch('transaction_payments', $this->id))->delay(0);
    }
}
