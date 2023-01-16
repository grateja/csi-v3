<?php

namespace App;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LagoonPerKilo extends Model
{
    use SoftDeletes, UsesUuid;

    public $timestamps = false;

    protected $fillable = [
        'id',
        'name',
        'price_per_kilo',
    ];
}
