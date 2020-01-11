<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Discount;

class DiscountsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $result = Discount::where(function($query) use ($request) {
            $query->where('name', 'like', "%$request->keyword%");
        })->get();

        return response()->json([
            'result' => $result,
        ], 200);
    }

    public function all() {
        $result = Discount::all();

        return response()->json([
            'result' => $result,
        ], 200);
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
            'name' => 'required',
            'discountType' => 'required',
        ];

        if($request->discountType == 'p') {
            $rules = array_merge($rules, [
                'percentage' => 'required',
            ]);
        }

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request) {
                $discount = Discount::create([
                    'name' => $request->name,
                    'discount_type' => $request->discountType['id'],
                    'percentage' => $request->percentage,
                ]);

                return response()->json([
                    'discount' => $discount,
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
        $discount = Discount::findOrFail($id);

        $rules = [
            'name' => 'required',
            'discountType' => 'required',
        ];

        if($request->discountType == 'p') {
            $rules = array_merge($rules, [
                'percentage' => 'required',
            ]);
        }

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request, $discount) {
                $discount->update([
                    'name' => $request->name,
                    'discount_type' => $request->discountType['id'],
                    'percentage' => $request->percentage,
                ]);

                return response()->json([
                    'discount' => $discount,
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
    public function delete($id)
    {
        $discount = Discount::findOrFail($id);
        if($discount->delete()) {
            return response()->json([
                'message' => 'Discount deleted successfully',
                'id' => $id,
            ], 200);
        }
    }
}
