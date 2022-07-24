<?php

namespace App\Http\Controllers;

use App\PartialPayment;
use Illuminate\Http\Request;

class PartialPaymentsController extends Controller
{
    public function show($partialPaymentId) {
        $partialPayment = PartialPayment::findOrFail($partialPaymentId);
        return response()->json([
            'partialPayment' => $partialPayment,
        ]);
    }
}
