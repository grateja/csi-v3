<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="/css/print.css">
    <title>Shop Preferences</title>
</head>
    <body>

        <div class="button-container">
            <button class="btn btn-primary" onclick="window.print()">PRINT</button>
        </div>

        <div class="main">
            <div class="header">
                <div class="text-center">{{$user_id}}</div>
                <div class="text-center large">{{$shop_name}}</div>
                <div class="text-center">{{$address}}</div>
                <div class="text-center">{{$shop_number}} / {{$shop_email}}</div>
                <div class="text-center">
                    <img class="text-center" src="/img/shop-pref-qr-code.png" alt="">
                </div>
            </div>
        </div>
    </body>
</html>

<script>
</script>
