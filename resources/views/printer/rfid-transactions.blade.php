<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <style>
        body, html {
            width: 11in;
            margin: 20px auto;
        }



        @media print{@page {size: landscape}}
    </style>
    <title>RFID Card Transactions</title>
</head>
<body>
    <table class="table table-bordered table-sm">
        <tr>
            <td colspan="2" class="text-right">Customer card: </td>
            <td colspan="3" class="text-left">{{$summary['customerCount']}}</td>
            <td colspan="3" class="text-left">P {{number_format($summary['customerCollections'], 2)}}</td>
        </tr>
        <tr>
            <td colspan="2" class="text-right">User card: </td>
            <td colspan="3" class="text-left">{{$summary['userCount']}}</td>
            <td colspan="3" class="text-left">P {{number_format($summary['userCollections'], 2)}}</td>
        </tr>
        <tr>
            <td colspan="2" class="text-right">Total: </td>
            <td colspan="3" class="text-left">{{$summary['totalCount']}}</td>
            <td colspan="3" class="text-left">P {{number_format($summary['totalSales'], 2)}}</td>
        </tr>
        <tr class="">
            <th>Date & Time</th>
            <th>Card owner</th>
            <th>Machine name</th>
            <th>Minutes</th>
            <th>Price</th>
            <th>RFID</th>
            <th>Card type</th>
        </tr>
        @foreach($result as $item)
            <tr>
                <td>{{$item->dateTimeStr}}</td>
                <td>{{$item->owner_name}}</td>
                <td>{{$item->machine_name}}</td>
                <td>{{$item->minutes}}</td>
                <td>P {{number_format($item->price, 2)}}</td>
                <td>{{$item->rfid}}</td>
                <td>{{$item->card_type == 'c' ? 'Customer card' : 'User card'}}</td>
            </tr>
        @endforeach
    </table>
</body>
</html>


