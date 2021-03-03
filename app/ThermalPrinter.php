<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\Printer;
use Carbon\Carbon;

class ThermalPrinter extends Model
{
    private $printer;
    public function hasError() {
        if($this->printer == null) {
            return [
                'message' => ['Printer not detected']
            ];
        }
        return false;
    }
    public function __construct()
    {
        if(file_exists("/dev/usb/lp0")) {
            $connector = new FilePrintConnector("/dev/usb/lp0");
            $this->printer = new Printer($connector);
        }
    }

    public function test($text) {
        $this->text($text);
        $this->printer->feed();
        $this->printer->close();
    }

    private function text($text) {
        $text = $text == null ? "" : $text;
        $this->printer->text($text);
    }

    private function printHeader() {
        $client = Client::first();
        $this->printer->setEmphasis(true);
        $this->printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
        $this->printer->setJustification(Printer::JUSTIFY_CENTER);
        $this->text($client->shop_name);
        $this->printer->feed();

        $this->printer->selectPrintMode(Printer::MODE_FONT_B);
        $this->printer->setEmphasis(false);

        $this->text($client->address);
        $this->printer->feed();

        $this->text($client->shop_number . '/' . $client->shop_email);
        $this->printer->feed(2);
    }

    private function printSubHeader($transaction) {
        $this->printer->initialize();
        $this->printDetail("      JO#", $transaction->job_order);
        $this->printDetail("     DATE", Carbon::createFromDate($transaction->date)->format('m/d/Y h:iA'));
        $this->printDetail(" CUSTOMER", $transaction->customer_name);
        $this->printDetail("    STAFF", $transaction->staff_name);
    }

    private function printQuote($text) {
        $this->printer->initialize();
        $this->printer->setTextSize(1, 2);
        $this->printer->setEmphasis(true);
        $this->printer->setJustification(Printer::JUSTIFY_CENTER);
        $this->text($text);
        $this->printer->feed(2);
    }

    private function printDetail($label, $value) {
        $this->text("$label: " . $value);
        $this->printer->feed();
    }

    private function cut() {
        $this->printer->setTextSize(1,1);
        $this->printer->feed();
        $this->printer->setJustification(Printer::JUSTIFY_CENTER);
        $this->printUnderline();
        $this->printer->feed(2);
        $this->printer->cut();
        $this->printer->close();
    }

    private function printBarcode($code) {
        $this->printer->setJustification(Printer::JUSTIFY_CENTER);
        $this->printer->setBarcodeHeight(5);
        $this->printer->barcode(substr($code, 1));
    }

    private function printItem($text, $price = 0, $quantity = null) {
        $left_cols = 22;
        $right_cols = 10;

        $price = $price ? $price : 0;

        $price = is_numeric($price) ? 'P' . number_format($price, 2, '.', ',') : $price;

        $print_name = substr(($quantity !== null ? "(" . $quantity . ")" : "") . $text, 0, $left_cols);
        $price_name = str_pad($price, 32 - strlen($print_name), ' ', STR_PAD_LEFT);
        $this->text($print_name . $price_name);
        $this->printer->feed();
    }

    private function printUnderline() {
        $this->text('--------------------------------');
        $this->printer->feed();
    }

    private function printSubtitle($text) {
        $this->printer->setFont(Printer::FONT_A);
        $this->printer->setEmphasis(true);
        $this->text($text);
        $this->printer->feed();
    }

    private function printList($items) {
        if($items) {
            foreach($items as $serviceItem) {
                $this->printItem($serviceItem['name'], $serviceItem['total_price'], $serviceItem['quantity']);
            }
        }
    }

    private function printServices($serviceItems) {
        if(count($serviceItems)) {
            $this->printer->feed();
            $this->printSubtitle("SERVICES");
            $this->printer->initialize();
            $this->printList($serviceItems);
        }
    }

    private function printProducts($productItems) {
        if(count($productItems)) {
            $this->printer->feed();
            $this->printSubtitle("PRODUCTS");
            $this->printer->initialize();
            $this->printList($productItems);
        }
    }

    public function claimStub($transaction) {
        $this->printHeader();
        $this->printQuote("*** CLAIM STUB ***");

        $this->printSubHeader($transaction);
        $this->printServices($transaction->posServiceItems);
        $this->printProducts($transaction->posProductItems);

        $this->printUnderline();
        $this->printItem("TOTAL AMOUNT",$transaction->total_price);
        $this->printer->feed();

        if($transaction->partialPayment && !$transaction->payment) {
            $this->printItem("Partial payment", $transaction->partialPayment['cash']);
            $this->printItem("Balance", $transaction->partialPayment['balance']);
        } else if(!$transaction->payment) {
            $this->printSubtitle("NOT PAID");
            $this->printer->initialize();
            $this->printItem("Balance", $transaction->total_price);
        }

        if($transaction->payment) {
            $this->printSubtitle("FULLY PAID");
            $this->printer->initialize();
            $this->printDetail("STAFF", $transaction->payment->user['name']);
        }

        $this->cut();
    }

    public function jobOrder($transaction) {
        $this->printHeader();
        $this->printQuote("*** JOB ORDER ***");

        $this->printSubHeader($transaction);

        $this->printServices($transaction->posServiceItems);
        $this->printProducts($transaction->posProductItems);

        $this->printUnderline();
        $this->printItem("TOTAL AMOUNT",$transaction->total_price);
        $this->printer->feed();

        if($transaction->partialPayment) {
            $this->printSubtitle("PARTIAL PAYMENT");
            $this->printer->initialize();
            $this->printItem("Date", Carbon::createFromDate($transaction->partialPayment['date'])->format('m/d/Y h:iA'));
            $this->printItem("Amount", $transaction->partialPayment['cash']);
            $this->printItem("Balance", $transaction->partialPayment['balance']);
            $this->printItem("Received by", $transaction->partialPayment['paid_to']);
            $this->printUnderline();
        }

        // Full Payment
        $this->printSubtitle("FULL PAYMENT");
        $this->printer->initialize();
        $this->printItem("Date paid", Carbon::createFromDate($transaction->date_paid)->format('m/d/Y h:iA'));

        if($transaction->payment->discount) {
            $this->printItem("{$transaction->payment->discount_name} (-{$transaction->payment->discount}%)", $transaction->payment->discount_in_peso);
        }

        if($transaction->payment->points) {
            $this->printItem("Points ({$transaction->payment->points}pts)", $transaction->payment->points_in_peso);
        }

        $this->printItem("Cash", $transaction->payment->cash);
        $this->printItem("Change", $transaction->payment->change);
        $this->printItem("Balance", 0);
        $this->printItem("Received by", $transaction->paidTo['name']);

        $this->cut();
    }

    public function loadTransaction($transaction) {
        $this->printHeader();

        $this->printQuote("*** RFID Load Top Up ***");

        $this->printer->initialize();

        $this->printItem("Date", $transaction->created_at->format('m/d/Y h:iA'));
        $this->printItem("Customer", $transaction->customer_name);
        $this->printItem("RFID", "#" . $transaction->rfid . "");
        $this->printItem("Amount", $transaction->amount);
        $this->printItem("Current balance", $transaction->current_balance);
        $this->printItem("Cash", $transaction->cash);
        $this->printItem("Change", $transaction->change);
        $this->printItem("Received by", $transaction->staff_name);
        $this->printItem("Remarks", $transaction->remarks);

        $this->cut();
    }

    private function printCaption($text) {
        $this->printer->initialize();
        $this->printer->setJustification(Printer::JUSTIFY_CENTER);
        $this->text("($text)");
        $this->printer->feed();
    }

    private function summaryJO($data) {
        $this->printer->initialize();
        $this->printer->setEmphasis(true);
        $this->printSubtitle("JOB ORDERS");
        $this->printer->initialize();
        $this->printItem("Fully paid", $data['fully_paid']['total_sales'], intVal($data['fully_paid']['total_jo']));
        $this->printItem("Partially paid", $data['partial_payments']['total_sales'], intVal($data['partial_payments']['total_jo']));
        $this->printItem("Unpaid", $data['unpaid']['total_sales'], intVal($data['unpaid']['total_jo']));
        $this->printUnderline();
        $this->printer->setEmphasis(true);
        $this->printItem("Total", $data['pos_transactions']['total_sales'], intVal($data['pos_transactions']['total_jo']));
        $this->printer->feed();
    }

    private function summaryUsedServices($data) {
        $this->printSubtitle("USED SERVICES");
        $this->printer->initialize();
        if(count($data)) {
            $data = collect($data);
            foreach($data as $item) {
                $this->printItem($item->name, $item->total_price, $item->quantity);
            }
            $this->printUnderline();
            $this->printer->setEmphasis(true);
            $this->printItem("Total", $data->sum('total_price'), $data->sum('quantity'));
            $this->printer->feed();
        } else {
            $this->printCaption("No used services");
            $this->printer->feed();
        }
    }

    private function summaryUsedProducts($data) {
        $this->printer->initialize();
        $this->printer->setEmphasis(true);
        $this->printSubtitle("USED PRODUCTS");
        $this->printer->initialize();
        if(count($data)) {
            $data = collect($data);
            foreach($data as $item) {
                $this->printItem($item->name, $item->total_price, $item->quantity);
            }
            $this->printUnderline();
            $this->printer->setEmphasis(true);
            $this->printItem("Total", $data->sum('total_price'), $data->sum('quantity'));
            $this->printer->feed();
        } else {
            $this->printCaption("No used products");
            $this->printer->feed();
        }
    }

    private function summaryRFID($data) {
        $this->printer->initialize();
        $this->printer->setEmphasis(true);
        $this->printSubtitle("RFID TAP CARD");
        $this->printer->initialize();
        $this->printItem("Master card", $data['users_card']);
        $this->printItem("Customer card", $data['customers_card']);
        $this->printUnderline();
        $this->printer->setEmphasis(true);
        $this->printItem("Total", $data['total_price']);
        $this->printer->feed();
    }

    private function summaryRFIDLoad($data) {
        $this->printer->initialize();
        $this->printer->setEmphasis(true);
        $this->printSubtitle("RFID LOAD");
        $this->printer->initialize();
        $this->printItem("Customer card", $data['total_price'], $data['total_count']);
        $this->printUnderline();
        $this->printer->setEmphasis(true);
        $this->printItem("Total", $data['total_price'], $data['total_count']);
        $this->printer->feed();
    }

    private function summaryTotalSales($data) {
        $this->printer->initialize();
        $this->printer->setEmphasis(true);
        $this->printer->setTextSize(1, 2);
        $this->printItem("TOTAL SALES", $data);
        $this->printer->feed();
    }

    private function summaryCollections($data) {
        $this->printer->initialize();
        $this->printSubtitle("COLLECTIONS");
        $this->printer->initialize();
        $this->printItem("Fully paid", $data['fullyPaid']);
        $this->printItem("Partially paid", $data['partiallyPaid']);
        $this->printItem("RFID Load", $data['rfidLoad']);
        $this->printItem("RFID Tap Card", $data['rfidTap']);
        $this->printUnderline();
        $this->printer->setEmphasis(true);
        $this->printItem("Total", $data['total']);
        $this->printer->feed();
    }

    private function summaryExpenses($data) {
        $this->printer->initialize();
        $this->printSubtitle("EXPENSES");
        $this->printer->initialize();
        $this->printItem("Product purchase", $data['productPurchases']['total_cost']);
        $this->printItem("Other", $data['otherExpenses']['other_expense']);
        $this->printUnderline();
        $this->printer->setEmphasis(true);
        $this->printItem("Total", $data['total']);
        $this->printer->feed();
    }

    private function summaryTotalDeposit($data) {
        $this->printer->initialize();
        $this->printer->setEmphasis(true);
        $this->printer->setTextSize(1, 2);
        $this->printItem("TOTAL DEPOSIT", $data);
        $this->printer->feed();
    }

    public function dailySummary($data) {
        $this->printHeader();
        $this->printQuote("** Daily Summary **");

        $this->printer->initialize();
        $this->printer->feed();

        $this->printDetail("DATE", $data['date']);
        $this->printUnderline();

        $this->printSubtitle("{$data['newCustomers']} new customers");
        $this->summaryJO($data['posSummary']);
        $this->summaryUsedServices($data['usedServices']);
        $this->summaryUsedProducts($data['usedProducts']);
        $this->summaryRFID($data['rfidCard']);
        $this->summaryRFIDLoad($data['rfidLoad']);
        $this->summaryTotalSales($data['totalSales']);
        $this->summaryCollections($data['collections']);
        $this->summaryExpenses($data['expenses']);
        $this->summaryTotalDeposit($data['totalDeposit']);

        $this->cut();
    }
}
