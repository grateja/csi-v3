<?php

namespace App;

use App\Jobs\AutoSynch;
use App\Traits\UsesSynch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UsesUuid;

class Rework extends Model
{
    use SoftDeletes, UsesUuid, UsesSynch;

    protected $fillable = [
        'id', 'remarks', 'customer_name', 'tag', 'machine_id', 'job_order', 'account_name', 'synched',
    ];

    public static function filterKeys($val) {
        $keys = [
            'customer_name' => 'Customer name',
            'job_order' => 'Job order number',
            'created_at' => 'Date activated',
            'machine_name' => 'Machine name',
            'default' => 'date',
        ];

        $filter = collect($keys)->filter(function($value) use ($val, $keys) {
            return strcasecmp($value, $val) == 0 && $keys['default'] != $val;
        })->keys();
        return $filter->first() ?? $keys['default'];
    }

    public function queSynch() {
        return (new AutoSynch('reworks', $this->id))->delay(5);
    }
}
