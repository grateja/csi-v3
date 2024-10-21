<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\OutSource;
use App\OutSourceLinen;
use App\OutSourceJobOrder;
use App\OutSourceJobOrderLinen;

class OutSourceJobOrderController extends Controller
{
    public function index(Request $request, $outSourceId) {
        $result = OutSourceJobOrder::resultWithTotal($outSourceId)
            ->orderByDesc('created_at');

        return response()->json([
            'result' => $result->paginate(10),
        ]);
    }

    public function show($outSourceId, $jobOrderId) {
        $jobOrder = OutSourceJobOrder::with('user', 'outSourceJobOrderLinens')->find($jobOrderId);
        if($jobOrder) {
            return response()->json([
                'jobOrder' => $jobOrder
            ]);
        } else {
            $jobOrderNumber = $this->generateJobOrderNumber($outSourceId);
            return response()->json([
                'jobOrderNumber' => $jobOrderNumber,
            ]);
        }
    }

    public function create(Request $request, $outSourceId) {
        $jobOrderNumber = $this->generateJobOrderNumber($outSourceId);
        $outSourceJobOrder = OutSourceJobOrder::create([
            'out_source_id' => $outSourceId,
            'job_order_number' => $jobOrderNumber,
            'user_id' => auth('api')->user()->id,
        ]);

        return response()->json([
            'jobOrder' => $outSourceJobOrder,
        ]);
    }

    public function addItem(Request $request, $jobOrderId) {
        $jobOrder = OutSourceJobOrder::findOrFail($jobOrderId);
        $linen = OutSourceLinen::findOrFail($request->linenId);

        $jobOrderLinen = OutSourceJobOrderLinen::where('out_source_job_order_id', $jobOrderId)
            ->where('out_source_linen_id', $request->linenId)
            ->where('degree_of_soil', $request->degree_of_soil)
            ->first();

        if($jobOrderLinen != null) {
            $jobOrderLinen->update([
                'quantity' => $jobOrderLinen->quantity + $request->quantity
            ]);
            $jobOrderLinen->fresh();
        } else {
            $jobOrderLinen = OutSourceJobOrderLinen::create([
                'out_source_job_order_id' => $jobOrderId,
                'out_source_linen_id' => $request->linenId,
                'quantity' => $request->quantity,
                'name' => $linen->name,
                'category' => $linen->category,
                'unit_price' => $linen[$request->degree_of_soil],
                'degree_of_soil' => $request->degree_of_soil,
            ]);
        }

        return response()->json([
            'jobOrderLinen' => $jobOrderLinen
        ]);
    }

    public function removeItem($jobOrderLinenId) {
        $jobOrderLinen = OutSourceJobOrderLinen::findOrFail($jobOrderLinenId);
        $jobOrderLinen->delete();
        return response()->json([
            'message' => 'Item removed successfully'
        ]);
    }

    public function delete($outSourceLinenId) {
        $outSourceLinen = OutSourceJobOrder::findOrFail($outSourceLinenId);
        $outSourceLinen->delete();

        return response()->json([
            'message' => 'OutSourceLinen deleted successfully',
        ]);
    }

    private function generateJobOrderNumber() {
        $jobOrder = OutSourceJobOrder::withTrashed()
            ->orderBy('job_order_number', 'desc')->first();

        if($jobOrder != null) {
            $startNumber = substr($jobOrder->job_order_number, -6) + 1;
        } else {
            $startNumber = 1;
        }

        return str_pad($startNumber, 5, '0', STR_PAD_LEFT);
    }
}
