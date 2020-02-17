<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <style>
        body, html {
            width: 8in;
        }

        table tr td:nth-child(1) {
            width: 5%;
        }
        table tr td:nth-child(2) {
            width: 10%;
        }
        table tr td:nth-child(3) {
            width: 12%;
        }
        table tr td:nth-child(4) {
            width: 12%;
        }
    </style>
    <title>Collections</title>
</head>
<body>
    <table>
        @foreach($result as $item)
            <tr>
                <td>{{$item->job_order}}</td>
                <td>{{$item->customer_name}}</td>
                <td>{{$item->dateStr}}</td>
                <td>{{$item->datePaidStr}}</td>
                <td>
                    <table>
                        @foreach($item->posServiceItems() as $serviceItem)
                            <tr>
                                <td>{{$serviceItem->name}}</td>
                                <td>{{$serviceItem->unit_price}}</td>
                                <td>{{$serviceItem->quantity}}</td>
                                <td>{{$serviceItem->total_price}}</td>
                            </tr>
                        @endforeach
                    </table>
                </td>
                <td>
                    <table>
                        @foreach($item->posProductItems() as $productItem)
                            <tr>
                                <td>{{$productItem->name}}</td>
                                <td>{{$productItem->unit_price}}</td>
                                <td>{{$productItem->quantity}}</td>
                                <td>{{$productItem->total_price}}</td>
                            </tr>
                        @endforeach
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="4">Summary</td>
                <td colspan="2">Total</td>
                <td>{{$item->posServiceSummary()['total_quantity']}}</td>
                <td>{{$item->posServiceSummary()['total_price']}}</td>
                <td colspan="2">Total</td>
                <td>{{$item->posProductSummary()['total_quantity']}}</td>
                <td>{{$item->posProductSummary()['total_price']}}</td>
            </tr>
        @endforeach
    </table>
</body>
</html>


