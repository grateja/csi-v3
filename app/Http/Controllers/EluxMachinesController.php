<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\EluxMachine;
use Illuminate\Http\Request;

class EluxMachinesController extends Controller
{
    public function index($machineType) {
        $result = EluxMachine::where('machine_type', $machineType)->get();
        return response()->json($result);
    }

    public function store(Request $request, $machineType) {
        $rules = [
            'machineName' => 'required',
            'ipAddress' => 'required',
            'model' => 'required',
            'machineType' => 'required',
            'stackOrder' => 'required|numeric',
        ];

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request, $machineType) {
                $machine = EluxMachine::create([
                    'model' => $request->model,
                    'machine_name' => $request->machineName,
                    'ip_address' => $request->ipAddress,
                    'machine_type' => $machineType,
                    'stack_order' => $request->stackOrder,
                ]);

                $this->dispatch($machine->queSynch());

                return response()->json([
                    'result' => $machine,
                ]);
            });
        }
    }

    public function updateSettings(Request $request, $machineId) {
        $rules = [
            'machineName' => 'required',
            'ipAddress' => 'required',
            'model' => 'required',
            'machineType' => 'required',
            'stackOrder' => 'required|numeric',
        ];

        if($request->validate($rules)) {
            $machine = EluxMachine::findOrFail($machineId);

            return DB::transaction(function () use ($request, $machine) {
                $machine->update([
                    'model' => $request->model,
                    'machine_name' => $request->machineName,
                    'ip_address' => $request->ipAddress,
                    'machine_type' => $request->machineType,
                    'stack_order' => $request->stackOrder,
                ]);

                $this->dispatch($machine->queSynch());

                return response()->json([
                    'result' => $machine,
                ]);
            });
        }
    }

    public function destroy($machineId) {
        $machine = EluxMachine::findOrFail($machineId);
        if($machine->delete()) {
            $this->dispatch($machine->queSynch());
            return response()->json([
                'success_message' => 'Machine Deleted'
            ]);
        }
    }

    public function nextStackOrder($machineType) {
        $stackOrder = 1;
        $machine = EluxMachine::where('machine_type', $machineType)->orderByDesc('stack_order')->first();
        if($machine != null) {
            $stackOrder = $machine->stack_order + 1;
        }
        return $stackOrder;
    }
}
