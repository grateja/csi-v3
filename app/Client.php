<?php

namespace App;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use UsesUuid;

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'user_id', 'shop_name', 'shop_email', 'address', 'shop_number',
    ];

    public function user() {
        return $this->belongsTo('App\User');
    }
}
