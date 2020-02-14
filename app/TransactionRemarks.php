<?php

namespace App;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;

class TransactionRemarks extends Model
{
    use UsesUuid;

    protected $fillable = [
        'remarks', 'added_by', 'transaction_id',
    ];
}
