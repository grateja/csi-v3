<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Expense;
use Illuminate\Support\Facades\DB;

class ExpensesController extends Controller
{
    public function autocomplete(Request $request) {
        $result = Expense::where('expense_type', 'like', "%$request->keyword%")
            ->selectRaw('expense_type as name, id')
            ->limit(10)->get();

        return response()->json([
            'data' => $result,
        ], 200);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sortBy = $request->sortBy ? $request->sortBy : 'date';
        $order = $request->orderBy ? $request->orderBy : 'desc';

        $result = Expense::where(function($query) use ($request) {
            $query->where('remarks', 'like', "%$request->keyword%")
                ->orWhere('staff_name', 'like', "%$request->keyword%");
        });

        if($request->date) {
            $result = $result->whereDate('transactions.saved', $request->date);
        }

        $result = $result->orderBy($sortBy, $order);

        return response()->json([
            'result' => $result->paginate(10),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'date' => 'required|date',
            'remarks' => 'required',
            'amount' => 'required|numeric',
        ];

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request) {
                $expense = Expense::create([
                    'date' => $request->date,
                    'remarks' => $request->remarks,
                    'amount' => $request->amount,
                    'staff_name' => auth('api')->user()->name,
                ]);

                return response()->json([
                    'expense' => $expense,
                ], 200);
            });
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'date' => 'required|date',
            'remarks' => 'required',
            'amount' => 'required|numeric',
        ];

        if($request->validate($rules)) {
            return DB::transaction(function () use ($id, $request) {
                $expense = Expense::findOrFail($id);
                $expense->update([
                    'date' => $request->date,
                    'remarks' => $request->remarks,
                    'amount' => $request->amount,
                    'staff_name' => auth('api')->user()->name,
                ]);

                return response()->json([
                    'expense' => $expense,
                ], 200);
            });
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
