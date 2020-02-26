<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <style>
        body, html {
            width: 11in;
            margin: 20px auto;
        }

        @media print{@page {size: landscape}}

        * {
            box-sizing: border-box;
        }

        table {
            border-spacing: 0;
        }

        tfoot {
            font-weight: bold;
        }

        .pl {
            padding-left: 3mm;
        }

        .sub-title {
            padding: 1mm;
            border-top: none;
            border-left: none;
            border-right: none;
            text-align: center;
        }

        .sub-summary {
            background: #ddd;
            font-weight: bold;
        }

        .sub-summary > td {
            border: none !important;
        }

        .job-order {
            width: 6%;
        }
        .customer-name {
            width: 10%;
        }
        .date {
            width: auto;
        }
        .date-paid {
            width: auto;
        }
        .services {
            width: 30%;
        }
        .products {
            width: 30%;
        }
        .eee {
            background: #eee;
        }
        .b {
            font-weight: bold;
        }
        table {
            width: 100%;
        }
        table.main td, th {
            border: 1px solid silver;
            /* padding: 0px;
            margin: 0px; */
        }

        .products-total, .services-total {
            width: auto;
        }

        .top-left {
            vertical-align: top;
            text-align: left;
        }
        .right {
            text-align: right;
        }
        .unit-price {
            width: 2cm;
        }
        .quantity {
            width: 2cm;
        }
        .total-price {
            width: 2cm;
        }

        table.items td {
            border: none;
        }
    </style>
    <title>Collections</title>
</head>
<body>
    <table class="main">
        <tr>
            <td colspan="4">Total job orders: </td>
            <td>{{$summary['totalCount']}}</td>
        </tr>
        <tr>
            <td colspan="4">Total collections: </td>
            <td>P {{number_format($summary['totalCollections'], 2)}}</td>
        </tr>
        <tr>
            <th>JO #</th>
            <th>C. Name</th>
            <th>Date & Time</th>
            <th>Paid</th>
            <th>Items</th>
        </tr>
        @foreach($result as $item)
            <tr>
                <td class="top-left job-order">{{$item->job_order}}</td>
                <td class="top-left customer-name">{{$item->customer_name}}</td>
                <td class="top-left date">{{$item->dateStr}}</td>
                <td class="top-left date-paid">{{$item->datePaidStr}}</td>
                <td class="top-left">
                    <table class="items">
                        @if(count($item->posServiceItems()))
                            <tr>
                                <th colspan="4" class="sub-title">Services</th>
                            </tr>
                            @foreach($item->posServiceItems() as $serviceItem)
                                <tr>
                                    <td class="pl">{{$serviceItem->name}}</td>
                                    <td class="unit-price">P {{ number_format($serviceItem->unit_price, 2) }}</td>
                                    <td class="quantity">{{ $serviceItem->quantity }}</td>
                                    <td class="total-price">P {{ number_format($serviceItem->total_price, 2) }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td class="b eee" colspan="2">Total</td>
                                <td class="b eee quantity">{{$item->posServiceSummary()['total_quantity']}}</td>
                                <td class="b eee total-price">P {{number_format($item->posServiceSummary()['total_price'], 2)}}</td>
                            </tr>
                        @endif
                        @if(count($item->posProductItems()))
                            <tr>
                                <th colspan="4" class="sub-title">Products</th>
                            </tr>
                            @foreach($item->posProductItems() as $productItem)
                                <tr>
                                    <td class="pl">{{$productItem->name}}</td>
                                    <td class="unit-price">P {{number_format($productItem->unit_price, 2)}}</td>
                                    <td class="quantity">{{$productItem->quantity}}</td>
                                    <td class="total-price">P {{number_format($productItem->total_price, 2)}}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td class="b eee" colspan="2">Total</td>
                                <td class="b eee quantity">{{$item->posProductSummary()['total_quantity']}}</td>
                                <td class="b eee total-price">P {{number_format($item->posProductSummary()['total_price'], 2)}}</td>
                            </tr>
                        @endif
                    </table>
                </td>
            </tr>
            <tr class="sub-summary">
                <td colspan="4">Summary</td>
                <td class="right pr">P {{number_format($item->total_price, 2)}}</td>
            </tr>
        @endforeach
    </table>
</body>
</html>


