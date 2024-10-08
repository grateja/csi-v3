<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/app.css">
    <style>
        body, html {
            width: 11in;
            margin: 20px auto;
        }

        .name {
            width: 40%;
        }

        .quantity {
            width: 20%;
        }

        .unit-price {
            width: 20%;
        }

        .amount {
            width: 20%;
        }

        @media print{
            @page {
                size: landscape;
            }
            body {
                font-size: 7pt;
            }
        }
    </style>
    <title>Collections</title>
</head>
<body>
    <table class="table table-bordered table-compressed">
        <tr>
            <td colspan="2" class="text-right">Job orders: </td>
            <td colspan="3" class="text-left">{{$summary['totalCount']}}</td>
        </tr>
        <tr>
            <td colspan="2" class="text-right">Total paid J.O.: </td>
            <td colspan="3" class="text-left">P {{number_format($summary['totalSales'], 2)}}</td>
        </tr>
        <tr>
            <td colspan="2" class="text-right">Discount: </td>
            <td colspan="3" class="text-left">P {{number_format($summary['totalSales'] - $summary['totalCollections'], 2)}}</td>
        </tr>
        <tr>
            <td colspan="2" class="text-right">Collections: </td>
            <td colspan="3" class="text-left">P {{number_format($summary['totalCollections'], 2)}}</td>
        </tr>
        <tr class="">
            <th>JO #</th>
            <th>Customer</th>
            <th>Date & Time</th>
            <th>Items</th>
            <th>Payment</th>
        </tr>
        @foreach($result as $item)
            <tr>
                <td class="top-left job-order">{{$item->job_order}}</td>
                <td class="top-left customer-name">{{$item->customer_name}}</td>
                <td class="top-left date">{{$item->dateStr}}</td>
                <td class="top-left">
                    <table class="table table-borderless table-compressed mb-0">
                        @if(count($item->posServiceItems()))
                            <tr class="text-center border-bottom">
                                <th class="sub-title name">Name</th>
                                <th class="sub-title unit-price">Unit price</th>
                                <th class="sub-title quantity">Quantity</th>
                                <th class="sub-title amount">Amount</th>
                            </tr>
                            @foreach($item->posServiceItems() as $serviceItem)
                                <tr class="text-center">
                                    <td class="text-left">{{$serviceItem->name}}</td>
                                    <td>P {{ number_format($serviceItem->unit_price, 2) }}</td>
                                    <td>{{ $serviceItem->quantity }}</td>
                                    <td>P {{ number_format($serviceItem->total_price, 2) }}</td>
                                </tr>
                            @endforeach
                        @endif
                        @if(count($item->posProductItems()))
                            @foreach($item->posProductItems() as $productItem)
                                <tr class="text-center">
                                    <td class="text-left">{{$productItem->name}}</td>
                                    <td>P {{number_format($productItem->unit_price, 2)}}</td>
                                    <td>{{$productItem->quantity}}</td>
                                    <td>P {{number_format($productItem->total_price, 2)}}</td>
                                </tr>
                            @endforeach
                        @endif
                        <tr class="font-weight-bold border-top border-dark">
                            <td colspan="3">Total</td>
                            <td class="text-right">P {{number_format($item->total_price, 2)}}</td>
                        </tr>
                    </table>
                </td>
                <td class="top-left date-paid">{{$item->datePaidStr}}
                    <table class="table table-compressed">
                        @if($item->payment['card_load_used'])
                        <tr>
                            <td>RFID:</td>
                            <td>{{$item->payment['rfid']}}</td>
                        </tr>
                        <tr>
                            <td>Card load:</td>
                            <td>{{number_format($item->payment['card_load_used'], 2)}}</td>
                        </tr>
                        @endif
                        @if($item->payment['discount'])
                        <tr>
                            <td>Discount:</td>
                            <td>P {{number_format($item->payment['discount_in_peso'], 2)}} ({{number_format($item->payment['discount'], 2)}} %)</td>
                        </tr>
                        @endif
                        <tr>
                            <td>Cash:</td>
                            <td>P {{number_format($item->payment['cash'], 2)}}</td>
                        </tr>
                        <tr>
                            <td>Change:</td>
                            <td>P {{number_format($item->payment['change'], 2)}}</td>
                        </tr>
                        <tr>
                            <td>Paid to:</td>
                            <td>{{$item->payment['paid_to']}}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        @endforeach
    </table>
</body>
</html>


