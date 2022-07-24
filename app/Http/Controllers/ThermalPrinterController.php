<?php

namespace App\Http\Controllers;

use App\Transaction;
use Illuminate\Http\Request;
use Mike42\Escpos\PrintConnectors\CupsPrintConnector;
use Mike42\Escpos\Printer;

class ThermalPrinterController extends Controller
{
    public function claimStub($transactionId) {
        $transaction = Transaction::findOrFail($transactionId);

        $connector = new CupsPrintConnector('xp58');
        $printer = new Printer($connector);

        $printer->text("Testing");
        $printer->cut();
        $printer->close();
    }
}
