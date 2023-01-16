@extends('layouts.print')

@section('content')
    <div class="control">
        <button onclick="printPreview()">print</button>
    </div>

    <div class="printer-wrapper">
        <div class="header center">
            <h3>{{$branch['name']}}</h3>
            <div>{{$branch['address']}}</div>
            <div class="small">{{$branch['contact_no']}} {{$branch['email']}}</div>
            <hr>
        </div>
        <table class="sub-header fullwidth">
            <tr>
                <td>Date :</td>
                <td class="right">{{$date}}</td>
            </tr>
            <tr>
                <td>Job order no. :</td>
                <td class="right">{{$jobOrder}}</td>
            </tr>
            <tr>
                <td>Customer name. :</td>
                <td class="right">{{$customerName}}</td>
            </tr>
            <tr>
                <td>Cashier :</td>
                <td>{{$payment->user->fullname}}</td>
            </tr>
        </table>
        <h3>Services</h3>
        <table class="fullwidth summary">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Quantity</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach($services as $service)
                    <tr>
                        <td>{{$service['name']}}</td>
                        <td class="center">{{$service['quantity']}}</td>
                        <td class="center">P {{$service['price']}}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td>TOTAL</td>
                    <td class="center">{{$serviceSummary['totalQuantity']}}</td>
                    <td class="center">P {{$serviceSummary['totalPrice']}}</td>
                </tr>
            </tfoot>
        </table>
        <h3>Products</h3>
        <table class="fullwidth summary">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Quantity</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                    <tr>
                        <td>{{$product['name']}}</td>
                        <td class="center">{{$product['quantity']}}</td>
                        <td class="center">P {{$product['price']}}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td>TOTAL</td>
                    <td class="center">{{$productSummary['totalQuantity']}}</td>
                    <td class="center">P {{$productSummary['totalPrice']}}</td>
                </tr>
            </tfoot>
        </table>

        <h3>Summary</h3>
        <table class="sub-header">
            <tr>
                <td>Sub total :</td>
                <td>P {{$payment['total_amount']}}</td>
            </tr>
            <tr>
                <td>Discount :</td>
                <td>P {{$discountInPeso}} ({{$payment->discount}}%)</td>
            </tr>
            @if($hasPoints)
                <tr>
                    <td>Points used :</td>
                    <td>{{$points}}</td>
                </tr>
            @endif
            @if(count($cardsUsed))
                <tr>
                    <td>Card(s) load used :</td>
                </tr>
                <tr>
                    @foreach($cardsUsed as $card)
                        <td>{{$card->rfidCard->rfid}} :</td>
                        <td>P{{$card->amount_deducted}}</td>
                    @endforeach
                </tr>
            @endif
            <tr>
                <td>Amount due :</td>
                <td>P {{$amountDue}}</td>
            </tr>
            <tr>
                <td>Cash :</td>
                <td>P {{$payment->cash}}</td>
            </tr>
            <tr>
                <td>Change :</td>
                <td>P {{$payment->changeStr}}</td>
            </tr>
        </table>

        <h3>Loyalty rewards</h3>
        <table class="fullwidth sub-header">
            <tr>
                <td class="right">Earned points :</td>
                <td>{{$earningPoints}}</td>
            </tr>
        </table>
        <hr>
        <h4 class="center">Thank you! Come again!</h4>
    </div>

@endsection

@section('styles')

    <style>
        * {
            box-sizing: border-box;
        }
        body{
            background: #fdfdfd;
            font-family: 'Arial';
            font-size: 14px;
        }

        h3 {
            margin-bottom: 2px;
        }

        .printer-wrapper {
            width: 4in;
            max-width: 100%;
            border: 1px dashed rgb(236, 236, 236);
            margin: 0px auto;
            padding: .4cm .3cm;
            background: white;
            box-shadow: 0px 0px 4px rgba(0, 0, 0, 0.082);
        }

        .center {
            text-align: center;
        }

        .header h4 {
            margin: 0;
            padding: 0;
        }

        .small {
            font-size: .8em;
        }
        .right {
            text-align: right;
        }
        .fullwidth {
            width: 100%;
        }

        table.sub-header {
            width: 100%;
        }

        tfoot {
            font-weight: bold;
        }
        tfoot td, thead th{
            border-top: 2px solid black;
            border-bottom: 2px solid black;
        }

        tfoot td {
            border-bottom-width: 1px;
        }

        thead th {
            border-top-width: 1px;
        }

        .summary td:nth-child(1) {
            width: 60%;
        }

        .summary td:nth-child(2) {
            width: 20%;
        }

        .summary td:nth-child(3) {
            width: 20%;
        }

        .sub-header td:nth-child(1) {
            text-align: right;
        }
        .sub-header td:nth-child(2) {
            text-align: left;
            font-weight: 600;
            width: 60%;
        }

        .sub-table td {
            font-weight: 100;
        }



        @media print {
            @page {
                width: 4in;
            }

            .control {
                display: none;
                position: fixed;
            }

            .printer-wrapper {
                border: none;
                box-shadow: none;
                margin: .4cm auto;
            }
        }
    </style>

@endsection

@section('scripts')
    <script>
        function printPreview() {
            window.print();
        }

        // setTimeout(printPreview, 500);
    </script>
@endsection
