<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UsesUuid;
use App\Traits\UsesSynch;

class Expense extends Model
{
    use SoftDeletes, UsesUuid, UsesSynch;

    protected $fillable = [
        'date', 'remarks', 'amount', 'staff_name', 'expense_type', 'synched',
    ];

    public function user() {
        return $this->belongsTo('App\User');
    }
}
