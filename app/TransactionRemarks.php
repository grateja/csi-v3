<?php

namespace App;

use App\Traits\UsesSynch;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionRemarks extends Model
{
    use UsesUuid, UsesSynch, SoftDeletes;

    protected $fillable = [
        'remarks', 'added_by', 'transaction_id', 'synched',
    ];
}
