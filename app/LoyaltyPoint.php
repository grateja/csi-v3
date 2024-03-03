<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UsesUuid;
use App\Traits\UsesSynch;

class LoyaltyPoint extends Model
{
    use SoftDeletes, UsesUuid, UsesSynch;

    protected $fillable = [
        'amount_in_peso',
    ];
}
