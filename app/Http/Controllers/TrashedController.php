<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TransactionRemarks;
use App\ServiceItemRemarks;
use App\ProductItemRemarks;

class TrashedController extends Controller
{
    public function byCustomer(Request $request) {
        $result = TransactionRemarks::with('user')->where(function() use ($request) {

        })->where([
            'branch_id' => auth('api')->user()->active_branch_id,
            'remarks_type' => 'v',
        ])->paginate(10);

        $result->getCollection()->transform(function($item) {
            return [
                'id' => $item->id,
                'date' => $item->created_at,
                'customerName' => $item->customer_name,
                'jobOrder' => $item->job_order,
                'remarks' => $item->remarks,
                'userName' => $item->user['fullname'],
            ];
        });

        return response()->json([
            'result' => $result,
        ], 200);
    }

    public function services(Request $request) {
        $result = ServiceItemRemarks::with('user')->where(function() use ($request) {

        })->where([
            'branch_id' => auth('api')->user()->active_branch_id,
        ])->paginate(10);

        $result->getCollection()->transform(function($item) {
            return [
                'id' => $item->id,
                'serviceName' => $item->item_name,
                'date' => $item->created_at,
                'customerName' => $item->customer_name,
                'jobOrder' => $item->job_order,
                'remarks' => $item->remarks,
                'userName' => $item->user['fullname'],
            ];
        });

        return response()->json([
            'result' => $result,
        ], 200);
    }

    public function products(Request $request) {
        $result = ProductItemRemarks::with('user')->where(function() use ($request) {

        })->where([
            'branch_id' => auth('api')->user()->active_branch_id,
        ])->paginate(10);

        $result->getCollection()->transform(function($item) {
            return [
                'id' => $item->id,
                'productName' => $item->item_name,
                'date' => $item->created_at,
                'customerName' => $item->customer_name,
                'jobOrder' => $item->job_order,
                'remarks' => $item->remarks,
                'userName' => $item->user['fullname'],
            ];
        });

        return response()->json([
            'result' => $result,
        ], 200);
    }
}
