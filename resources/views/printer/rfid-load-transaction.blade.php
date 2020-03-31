<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <title>RFID Load transaction</title>
    <style>
            body, html {
                background: #eee;
            }
            .button-container {
                position: fixed;
                right: 10px;
                top: 10px;
            }
            @media print {
                .button-container {
                    display: none;
                }
            }
            .main {
                background: white;
                border: 1px solid #eee;
                box-shadow: 10px 10px 10px #ddd;
                padding: .5cm;
                max-width: 80%;
                margin: 20px auto;
            }
        </style>
    </head>
<body>
    <div class="button-container">
        <button class="btn btn-primary" onclick="window.print()">PRINT</button>
    </div>
    <div class="main">
        <div class="header">
            <h4 class="text-center">{{$shop_name}}</h4>
            <div class="text-center">{{$shop_address}}</div>
            <div class="text-center">{{$shop_number}} / {{$shop_email}}</div>
            <hr>
            <h3 class="text-center">*** RFID LOAD TOP UP ***</h3>
        </div>
        <dl class="row">
            <dt class="col-6 text-right">Date:</dt>
            <dd class="col-6">{{$created_at->format('M-d, Y h:i A')}}</dd>

            <dt class="col-6 text-right">Customer name:</dt>
            <dd class="col-6">{{$customer_name}}</dd>

            <dt class="col-6 text-right">RFID:</dt>
            <dd class="col-6">{{$rfid}}</dd>

            <dt class="col-6 text-right">Amount:</dt>
            <dd class="col-6">{{number_format($amount, 2)}}</dd>

            <dt class="col-6 text-right">Remaining balance:</dt>
            <dd class="col-6">{{number_format($remaining_balance, 2)}}</dd>

            <dt class="col-6 text-right">Current balance:</dt>
            <dd class="col-6">{{number_format($current_balance, 2)}}</dd>

            <dt class="col-6 text-right">Cash:</dt>
            <dd class="col-6">{{number_format($cash, 2)}}</dd>

            <dt class="col-6 text-right">Change:</dt>
            <dd class="col-6">{{number_format($change, 2)}}</dd>

            <dt class="col-6 text-right">Staff name:</dt>
            <dd class="col-6">{{$staff_name}}</dd>

            <dt class="col-6 text-right">Remarks:</dt>
            <dd class="col-6">{{$remarks}}</dd>
        </dl>
        <hr>
        <div class="footer text-center">
            <div>This is not an official receipt</div>
            <div>This is not a sales invoice</div>
            <div>*** CUSTOMER COPY ***</div>
        </div>
    </div>
</body>
</html>
