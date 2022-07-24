<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UsesUuid;

class LoyaltyPoint extends Model
{
    use SoftDeletes, UsesUuid;

    protected $fillable = [
        'amount_in_peso',
    ];
}
