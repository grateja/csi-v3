<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuid;
use Illuminate\Support\Str;

class MonitorChecker extends Model
{
    use UsesUuid;

    protected $fillable = [
        'id', 'transaction_id', 'job_order', 'action', 'token',
    ];

    public static function hasQue($transactionId) {
        $monitorChecker = MonitorChecker::firstOrCreate(['id' => 'default']);
        $monitorChecker->update([
            'transaction_id' => $transactionId,
            'token' => Str::uuid(),
            'action' => 'hasQue',
        ]);
    }

    public static function selectCustomer($customerId) {
        $monitorChecker = MonitorChecker::firstOrCreate(['id' => 'default']);
        $monitorChecker->update([
            'transaction_id' => null,
            'token' => $customerId,
            'action' => 'select-customer',
        ]);
    }

    public static function idle() {
        $monitorChecker = MonitorChecker::firstOrCreate(['id' => 'default']);
        $monitorChecker->update([
            'transaction_id' => null,
            'token' => Str::uuid(),
            'action' => 'idle',
        ]);
    }

    public static function refreshToken() {
        $monitorChecker = MonitorChecker::firstOrCreate(['id' => 'default']);
        $monitorChecker->update([
            'token' => Str::uuid(),
        ]);
    }
}
