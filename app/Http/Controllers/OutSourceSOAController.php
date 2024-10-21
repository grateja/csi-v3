<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\OutSource;
use App\OutSourceJobOrder;
use App\OutSourceStatementOfAccount;

class OutSourceSOAController extends Controller
{
    public function index(Request $request, $outSourceId) {
        $result = OutSourceStatementOfAccount::where('out_source_id', $outSourceId)->paginate(10);

        return response()->json([
            'result' => $result
        ]);
    }

    public function prepareOrEdit($soaId, $outSourceId) {
        $soa = OutSourceStatementOfAccount::with([
            'outSourceJobOrders' => function($query) {
                $query->orderBy('created_at');
            }
        ])->find($soaId);
        
        $jobOrders = OutSourceJobOrder::resultWithTotal($outSourceId)
            ->whereNull('out_source_statement_of_account_id')
            ->orderBy('created_at')
            ->get();

        if(count($jobOrders) > 0) {
            $startDate = $jobOrders->first()->created_at->format('Y-m-d');
            $endDate = $jobOrders->last()->created_at->format('Y-m-d');
        } else {
            $startDate = \Carbon\Carbon::now()->format('Y-m-d');
            $endDate = \Carbon\Carbon::now()->format('Y-m-d');
        }

        if($soa == null) {
            $soaNumber = $this->generateSOANumber();

            return response()->json([
                'soa' => [
                    'soa_number' => $soaNumber,
                    'total_amount' => $jobOrders->sum('total'),
                    'out_source_job_orders' => $jobOrders,
                ],
                'start_date' => $startDate,
                'end_date' => $endDate,
                'action' => 'insert'
            ]);
        } else {
            return response()->json([
                'soa' => $soa,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'action' => 'update',
            ]);
        }


        return response()->json([
            'jobOrders' => $jobOrders
        ]);
    }

    public function insert(Request $request) {
        $rules = [
            'soa_number' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ];

        $request->validate($rules);

        return DB::transaction(function () use ($request) {
            $soa = OutSourceStatementOfAccount::create($request->only([
                'out_source_id',
                'soa_number',
                'remarks',
                'vat',
            ]));

            $jobOrders = OutSourceJobOrder::where('out_source_id', $request->out_source_id)
                ->whereNull('out_source_statement_of_account_id')
                ->whereDate('created_at', '>=', $request->start_date)
                ->whereDate('created_at', '<=', $request->end_date);
            
            $jobOrders->update([
                'out_source_statement_of_account_id' => $soa->id,
            ]);
        });

    }

    private function generateSOANumber() {
        // $outSource = OutSource::find($outSourceId);
        $soa = OutSourceStatementOfAccount::withTrashed()
            ->orderBy('soa_number', 'desc')->first();

        if($soa != null) {
            $startNumber = substr($soa->soa_number, -6) + 1;
        } else {
            $startNumber = 1;
        }

        // $abbr = $outSource->abbr;

        return str_pad($startNumber, 6, '0', STR_PAD_LEFT);
    }
}
