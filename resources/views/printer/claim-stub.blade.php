<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="/css/print.css">
    <title>Claim stub</title>
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
                <h2 class="text-center">*** CLAIM STUB ***</h2>
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
                        <td class="text-right">STATUS :</td>
                        <td>{{$status}}</td>
                    </tr>
                    <tr>
                        <td class="text-right">TOTAL AMOUNT :</td>
                        <td>P {{number_format($total_amount, 2)}}</td>
                    </tr>
                </table>
            </div>
            <hr>
            <div class="footer text-center">
                <div>This is not an official receipt</div>
                <div>This is not a sales invoice</div>
                <div>*** CUSTOMER COPY ***</div>
            </div>
        </div>
    </body>
</html>

<script>
</script>
