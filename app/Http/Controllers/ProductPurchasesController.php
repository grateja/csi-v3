<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ProductPurchase;
use Illuminate\Support\Facades\DB;

class ProductPurchasesController extends Controller
{
    public function index(Request $request) {
        $summary = null;

        $result = ProductPurchase::with('product', 'user')
            ->where(function($query) use ($request) {
                $query->whereHas('product', function($query) use ($request) {
                    $query->where('name', 'like', "%$request->keyword%");
                });
            });
        $resultData = $result->paginate(10);

        $resultData->getCollection()->transform(function($item) {
            return [
                'date' => $item->date,
                'productName' => $item->product['name'],
                'quantity' => $item->quantity,
                'unitCost' => $item->unit_cost,
                'totalCost' => $item->unit_cost * $item->quantity,
                'receipt' => $item->receipt,
                'remarks' => $item->remarks,
            ];
        });

        if($result->count()) {
            $summary = $result->skip(0)->groupBy('product_id')
                ->select(DB::raw('product_id, SUM(quantity) as quantitySum, SUM(unit_cost * quantity) as priceSum'))
                ->get();
            $summary = collect($summary)->transform(function($item) {
                return [
                    'name' => $item->branchProduct->product['name'],
                    'quantity' => $item->quantitySum,
                    'priceSum' => $item->priceSum,
                ];
            })->sortBy('quantity');
        }

        return response()->json([
            'result' => $resultData,
            'summary' => $summary,
        ], 200);
    }
}
