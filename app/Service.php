<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UsesUuid;

class Service extends Model
{
    use SoftDeletes, UsesUuid;

    protected $fillable = [
        'service_type', 'name', 'description', 'barcode', 'img_path', 'add_super_wash', 'price'
    ];
}
