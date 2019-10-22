<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UsesUuid;

class Discount extends Model
{
    use SoftDeletes, UsesUuid;

    protected $fillable = [
        'name', 'discount_type', 'percentage',
    ];
}
