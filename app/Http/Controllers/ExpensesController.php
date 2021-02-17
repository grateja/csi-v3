<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Expense;
use App\ProductPurchase;
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
        $sortBy = Expense::filterKeys($request->sortBy);
        // $sortBy = $request->sortBy ? $request->sortBy : 'date';
        $order = $request->orderBy ? $request->orderBy : 'desc';

        $result = Expense::select(['id', 'remarks', 'staff_name', 'date', 'amount', 'created_at', DB::raw('"exp" as type')])->where(function($query) use ($request) {
            $query->where('remarks', 'like', "%$request->keyword%")
                ->orWhere('staff_name', 'like', "%$request->keyword%");
        });

        $productPurchases = ProductPurchase::select(DB::raw('id, CONCAT("Purchase of \"", product_name, "\"") as remarks, staff_name, date, product_purchases.quantity * product_purchases.unit_cost as amount, created_at, "prd" as type'))->where(function($query) use ($request) {
            $query->where('product_name', 'like', "%$request->keyword%")
                ->orWhere('staff_name', 'like', "%$request->keyword%");
        });

        if($request->date) {
            $result = $result->whereDate('date', $request->date);
            $productPurchases = $productPurchases->whereDate('date', $request->date);
        }

        $result = $result->union($productPurchases);

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

                $this->dispatch($expense->queSynch());

                $expense['type'] = 'exp';

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

                $this->dispatch($expense->queSynch());

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
    public function deleteExpense($expenseId)
    {
        $expense = Expense::findOrFail($expenseId);
        if($expense) {
            $expense->delete();
            $this->dispatch($expense->queSynch());

            return response()->json([
                'expense' => $expense,
            ]);
        }
    }
}
