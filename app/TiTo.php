<?php

namespace App;

use App\Jobs\AutoSynch;
use App\Traits\UsesSynch;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;

class TiTo extends Model
{
    use UsesSynch, UsesUuid;

    protected $fillable = [
        'id', 'user_id', 'user_name', 'time_out', 'remarks', 'device', 'synched', 'created_at', 'updated_at',
    ];

    public static function filterKeys($val) {
        $keys = [
            'created_at' => 'Time in',
            'time_out' => 'Time out',
            'user_name' => 'Staff name',
            'default' => 'created_at',
        ];

        $filter = collect($keys)->filter(function($value) use ($val, $keys) {
            return strcasecmp($value, $val) == 0 && $keys['default'] != $val;
        })->keys();
        return $filter->first() ?? $keys['default'];
    }

    public function queSynch() {
        return (new AutoSynch('ti_tos', $this->id))->delay(5);
    }
}
