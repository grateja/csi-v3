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
        'changeStr'
    ];

    protected $fillable = [
        'transaction_id', 'cash', 'points', 'discount', 'total_amount', 'change', 'user_id', 'points_in_peso', 'balance', 'total_cash'
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
}
