<?php

namespace App\Http\Controllers;

use App\ProductTransactionItem;
use Illuminate\Http\Request;

class DeletedItemsController extends Controller
{
    public function products(Request $request) {
        $result = ProductTransactionItem::onlyTrashed()->where(function($query) {

        });

        return response()->json($result);
    }
}
