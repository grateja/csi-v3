<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuid;

class MonitorChecker extends Model
{
    use UsesUuid;

    protected $fillable = [
        'id', 'transaction_id', 'job_order', 'action', 'token',
    ];
}
