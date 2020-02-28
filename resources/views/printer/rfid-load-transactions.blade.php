<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <style>
        body, html {
            width: 8in;
            margin: 20px auto;
        }



        /* @media print{@page {size: landscape}} */
    </style>
    <title>RFID Load Transactions</title>
</head>
<body>
    <table class="table table-bordered table-sm">
        <tr>
            <td class="text-right">Transactions: </td>
            <td class="text-left">{{$summary['totalCount']}}</td>
            <td colspan="3" class="text-left">P {{number_format($summary['totalPrice'], 2)}}</td>
        </tr>
        <tr class="">
            <th>Date & Time</th>
            <th>Customer</th>
            <th>RFID</th>
            <th>Amount</th>
            <th>Remarks</th>
        </tr>
        @foreach($result as $item)
            <tr>
                <td>{{$item->dateTimeStr}}</td>
                <td>{{$item->customer_name}}</td>
                <td>{{$item->RFID}}</td>
                <td>P {{number_format($item->amount, 2)}}</td>
                <td>{{$item->remarks}}</td>
            </tr>
        @endforeach
    </table>
</body>
</html>


