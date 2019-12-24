<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Product;
use App\ProductPurchase;

class ProductsController extends Controller
{
    public function index(Request $request) {
        $products = Product::where(function($query) use ($request) {
            $query->where('name', 'like', "%$request->keyword%");
        })->paginate(10);

        return response()->json([
            'result' => $products
        ], 200);
    }

    public function store($userId, Request $request) {
        $rules = [
            'name' => 'required|unique',
        ];

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request, $userId) {

                $product = Product::create([
                    'name' => $request->name,
                    'description' => $request->description,
                    'selling_price' => $request->unitPrice,
                    'initial_stock' => $request->initialStock,
                    'minimum_stock' => $request->minimumStock,
                ]);

                return response()->json([
                    'product' => $product,
                ], 200);
            });
        }
    }

    public function update($id, Request $request) {
        $rules = [
            'name' => 'required',
        ];

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request, $id) {
                $product = Product::findOrFail($id);

                $product->update([
                    'name' => $request->name,
                    'description' => $request->description,
                    'selling_price' => $request->unitPrice,
                    'initial_stock' => $request->initialStock,
                    'minimum_stock' => $request->minimumStock,
                ]);

                return response()->json([
                    'product' => $product,
                ], 200);
            });
        }
    }

    public function show($id) {
        $product = Product::find($id);

        return response()->json([
            'product' => $product,
        ], 200);
    }

    public function addStock($productId, Request $request) {
        $product = Product::findOrFail($productId);

        $rules = [
            'date' => 'required|date',
            'quantity' => 'required|numeric|min:1',
            'unitCost' => 'numeric|nullable',
        ];

        if($request->validate($rules)) {
            $productPurchase = ProductPurchase::create([
                'date' => $request->date,
                'product_id' => $product->id,
                'receipt' => $request->receipt,
                'quantity' => $request->quantity,
                'unit_cost' => $request->unitCost,
                'remarks' => $request->remarks,
                'user_id' => auth('api')->id(),
            ]);

            return response()->json([
                'branchProduct' => $product->fresh(),
            ], 200);
        }
    }
}
