<?php

namespace App\Http\Controllers;

use App\MachineUsage;
use Illuminate\Http\Request;

class MachineUsagesController extends Controller
{
    public function deleteUsage($machineUsageId) {
        $machineUsage = MachineUsage::findOrFail($machineUsageId);
        if($machineUsage->delete()) {
            $this->dispatch($machineUsage->queSynch());

            return response()->json([
                'machineUsage' => $machineUsage,
            ]);
        }
    }
}
