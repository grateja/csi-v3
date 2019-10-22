<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UsesUuid;

class JobOrderFormat extends Model
{
    use SoftDeletes, UsesUuid;
    protected $fillable = [
        'char_count', 'prefix', 'start_number', 'format',
    ];
}
