<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/app.css">
    <title>RFID Tap Card</title>
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
                body {
                    font-size: 7pt;
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
            <h3 class="text-center">*** RFID TAP CARD ***</h3>
        </div>
        <dl class="row">
            <dt class="col-6 text-right">Date:</dt>
            <dd class="col-6">{{$created_at->format('M-d, Y h:i A')}}</dd>

            <dt class="col-6 text-right">Card Owner:</dt>
            <dd class="col-6">{{$owner_name}}</dd>

            <dt class="col-6 text-right">RFID:</dt>
            <dd class="col-6">{{$rfid}}</dd>

            <dt class="col-6 text-right">Amount:</dt>
            <dd class="col-6">{{number_format($amount, 2)}}</dd>

            <dt class="col-6 text-right">Machine:</dt>
            <dd class="col-6">{{$machine_name}}</dd>

            <dt class="col-6 text-right">Minutes:</dt>
            <dd class="col-6">{{$minutes}}</dd>
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
