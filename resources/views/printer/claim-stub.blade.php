<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <title>Claim stub</title>
    <style>
        body, html {
            background: #eee;
        }
        .button-container {
            position: fixed;
            right: 10px;
            top: 10px;
        }
        .large {
            font-size: 1.5em;
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
                <h3 class="text-center">*** CLAIM STUB ***</h3>
            </div>
            <hr>
            <dl class="row">
                <dt class="col-6 text-right">Job order:</dt>
                <dd class="col-6">
                    {{$job_order}}
                </dd>

                <dt class="col-6 text-right">Date:</dt>
                <dd class="col-6">
                    {{$date}}
                </dd>

                <dt class="col-6 text-right">Customer name:</dt>
                <dd class="col-6">
                    {{$customer_name}}
                </dd>

                <dt class="col-6 text-right">Status:</dt>
                <dd class="col-6">
                    {{$status}}
                </dd>

                <dt class="col-6 text-right">Total amount:</dt>
                <dd class="col-6">
                    P {{number_format($total_amount, 2)}}
                </dd>
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

<script>
</script>
