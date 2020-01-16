<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Http\Request;
use App\ProductPurchase;
use Illuminate\Support\Facades\DB;

class ProductPurchasesController extends Controller
{
    // public function index(Request $request) {
    //     $summary = null;

    //     $result = ProductPurchase::with('product', 'user')
    //         ->where(function($query) use ($request) {
    //             $query->whereHas('product', function($query) use ($request) {
    //                 $query->where('name', 'like', "%$request->keyword%");
    //             });
    //         });
    //     $resultData = $result->paginate(10);

    //     $resultData->getCollection()->transform(function($item) {
    //         return [
    //             'date' => $item->date,
    //             'productName' => $item->product['name'],
    //             'quantity' => $item->quantity,
    //             'unitCost' => $item->unit_cost,
    //             'totalCost' => $item->unit_cost * $item->quantity,
    //             'receipt' => $item->receipt,
    //             'remarks' => $item->remarks,
    //         ];
    //     });

    //     if($result->count()) {
    //         $summary = $result->skip(0)->groupBy('product_id')
    //             ->select(DB::raw('product_id, SUM(quantity) as quantitySum, SUM(unit_cost * quantity) as priceSum'))
    //             ->get();
    //         $summary = collect($summary)->transform(function($item) {
    //             return [
    //                 'name' => $item->branchProduct->product['name'],
    //                 'quantity' => $item->quantitySum,
    //                 'priceSum' => $item->priceSum,
    //             ];
    //         })->sortBy('quantity');
    //     }

    //     return response()->json([
    //         'result' => $resultData,
    //         'summary' => $summary,
    //     ], 200);
    // }

    public function index(Request $request) {
        $sortBy = $request->sortBy ? $request->sortBy : 'date';
        $order = $request->orderBy ? $request->orderBy : 'desc';

        $result = DB::table('product_purchases')
            ->whereNull('product_purchases.deleted_at')
            ->whereNull('products.deleted_at')
            ->where(function($query) use ($request) {
                $query->where('products.name', 'like', "%$request->keyword%")
                    ->orWhere('remarks', 'like', "%$request->keyword%")
                    ->orWhere('receipt', 'like', "%$request->keyword%");
            })
            ->join('products', 'products.id', '=', 'product_purchases.product_id')
            ->selectRaw('products.name, product_purchases.id, date, quantity, unit_cost, remarks, receipt');

        if($request->date) {
            $result = $result->whereDate('date', $request->date);
        }

        $result = $result->orderBy($sortBy, $order);

        return response()->json([
            'result' => $result->paginate(10),
        ]);
    }

    public function store(Request $request) {
        $rules = [
            'productName' => 'required',
            'date' => 'required|date',
            'quantity' => 'required|numeric',
            'unitCost' => 'required|numeric',
        ];

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request) {
                $product = Product::where('name', $request->productName)->first();
                if($product == null) {
                    return response()->json([
                        'errors' => [
                            'productName' => ['Product was not found']
                        ]
                    ], 422);
                }

                $prodcutPurchase = ProductPurchase::create([
                    'date' => $request->date,
                    'product_id' => $product->id,
                    'receipt' => $request->receipt,
                    'quantity' => $request->quantity,
                    'unit_cost' => $request->unitCost,
                    'remarks' => $request->remarks,
                    'user_id' => auth('api')->id(),
                ]);

                $product->increment('current_stock', $request->quantity);

                return response()->json([
                    'productPurchase' => [
                        'id' => $prodcutPurchase->id,
                        'date' => $prodcutPurchase->date,
                        'name' => $prodcutPurchase->product->name,
                        'quantity' => $prodcutPurchase->quantity,
                        'unit_cost' => $prodcutPurchase->unit_cost,
                        'remarks' => $prodcutPurchase->remarks,
                    ]
                ]);
            });
        }
    }

    public function deleteProductPurchase($productPurchaseId) {
        return DB::transaction(function () use ($productPurchaseId) {
            $prodcutPurchase = ProductPurchase::findOrFail($productPurchaseId);

            $product = Product::findOrFail($prodcutPurchase->product_id);
            $product->decrement('current_stock', $prodcutPurchase->quantity);
            if($product->current_stock < 0) {
                $product->update([
                    'current_stock' => 0,
                ]);
            }

            $prodcutPurchase->delete();
        });
    }
}
