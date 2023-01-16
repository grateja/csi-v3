<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{$date}}</title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
</head>
<body>
    <div class="controls">
        <button onclick="window.print()" class="btn-print btn btn-primary">Print</button>
    </div>
    <div class="paper">
        {{$date}}
    </div>

    <script>

    </script>
    <style>
        @media print{
            .controls {
                display: none;
            }
            .paper {
                /* width: 8in; */
                /* margin: 0px auto; */
                /* box-shadow: none; */
                border: 1px solid red;
            }
            @page {
                size: 10cm ;
            }
        }
        @media screen {
            .controls {
                display: block;
                position: fixed;
                background: gray;
            }
            .btn-print {
            }
            .paper {
                border: 1px solid red;
                width: 8in;
                max-width: 100%;
                margin: 20px auto;
                box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.493);
            }
        }
    </style>
</body>
</html>
