<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UsesUuid;

class PaymentRemarks extends Model
{
    use SoftDeletes, UsesUuid;

    protected $fillable = [
        'transaction_payment_id', 'user_id', 'remarks'
    ];
}
