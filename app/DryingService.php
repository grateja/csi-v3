<?php

namespace App;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DryingService extends Model
{
    use SoftDeletes, UsesUuid;

    protected $fillable = [
        'name', 'description', 'img_path', 'price', 'machine_type', 'minutes', 'points',
    ];
}
