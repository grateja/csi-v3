<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="/css/print.css">
    <title>Print Job Order</title>
</head>
<body>
    <div class="button-container">
        <button class="btn btn-primary" onclick="window.print()">PRINT</button>
    </div>

    <div class="main">
        <div class="header">
            <div class="text-center large">{{$shop_name}}</div>
            <div class="text-center">{{$shop_address}}</div>
            <div class="text-center">{{$shop_number}} / {{$shop_email}}</div>
            <hr>
            <h2 class="text-center">*** JOB ORDER ***</h2>
            <hr>
            <table>
                <tr>
                    <td class="text-right">JOB ORDER :</td>
                    <td>{{$job_order}}</td>
                </tr>
                <tr>
                    <td class="text-right">DATE :</td>
                    <td>{{$date}}</td>
                </tr>
                <tr>
                    <td class="text-right">CUSTOMER :</td>
                    <td>{{$customer_name}}</td>
                </tr>
                <tr>
                    <td class="text-right">STAFF NAME :</td>
                    <td>{{$staff_name}}</td>
                </tr>
            </table>
        </div>
        <br />
        <table>
            @if(count($posServiceItems))
                <tr>
                    <th class="text-left">SERVICES</th>
                    <th class="text-right">PRICE</th>
                </tr>

                @foreach($posServiceItems as $item)
                    <tr>
                        <td class="pl-4">({{$item['quantity']}}){{$item['name']}}</td>
                        <td class="text-right">{{$item['total_price'] ? 'P ' . number_format($item['total_price'], 2) : 'FREE'}}</td>
                    </tr>
                @endforeach
                <tr class=" font-weight-bold">
                    <td class="pl-1">Total</td>
                    <td class="text-right">P {{number_format($posServiceSummary['total_price'], 2)}}</td>
                </tr>
            @endif

            <tr>
                <td>&nbsp;</td>
            </tr>

            @if(count($posProductItems))
                <tr>
                    <th class="text-left">PRODUCTS</th>
                    <th class="text-right">PRICE</th>
                </tr>
                @foreach($posProductItems as $item)
                    <tr>
                        <td class="pl-4">({{$item['quantity']}}){{$item['name']}}</td>
                        <td class="text-right">{{$item['total_price'] ? 'P ' . number_format($item['total_price'], 2) : 'FREE'}}</td>
                    </tr>
                @endforeach
                <tr class=" font-weight-bold">
                    <td class="pl-1">Total</td>
                    <td class="text-right">P {{number_format($posProductSummary['total_price'], 2)}}</td>
                </tr>
            @endif
            <tr>
                <td>&nbsp;</td>
            </tr>
            <tr class="font-weight-bold large">
                <td>Grand total</td>
                <td class="text-right">P {{number_format($total_amount, 2)}}</td>
            </tr>
        </table>

        <br />

        <table>
            <tr>
                <td class="text-right">DATE PAID:</td>
                <td>{{$date_paid}}</td>
            </tr>
            <tr>
                <td class="text-right">PAID TO:</td>
                <td>{{$paid_to}}</td>
            </tr>
            <tr>
                <td class="text-right">CASH:</td>
                <td>P {{number_format($cash, 2)}}</td>
            </tr>
            <tr>
                <td class="text-right">CHANGE:</td>
                <td>P {{number_format($change, 2)}}</td>
            </tr>
            @if($points)
                <tr>
                    <td class="text-right">Points used:</td>
                    <td>
                        P {{number_format($points_in_peso, 2)}} ({{number_format($points, 1)}} points)
                    </td>
                </tr>
            @endif
            @if($discount)
                <tr>
                    <td class="text-right">Discount:</td>
                    <td>
                        P {{number_format($discount_in_peso, 2)}} ({{number_format($discount, 1)}}%)
                    </td>
                </tr>
            @endif
            @if($rfid)
                <tr>
                    <td class="text-right">RFID:</td>
                    <td>
                        P {{number_format($card_load_used, 2)}} ({{$rfid}})
                    </td>
                </tr>
            @endif
        </table>

        <br>

        <div class="footer text-center">
            <div>This is not an official receipt</div>
            <div>This is not a sales invoice</div>
            <div>*** CUSTOMER COPY ***</div>
        </div>

    </div>
    </body>
</html>
