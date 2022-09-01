<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="{{ asset('css/j.css') }}">
    <link rel="stylesheet" href="{{ asset('css/jquery.easy_slides.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">

    <!-- Scripts -->
    <!-- <script src="{{ asset('js/app.js') }}" defer></script> -->
    <!-- <script src="{{ asset('js/news.js') }}"></script> -->

    <!-- Fonts -->
    <!-- <link rel="dns-prefetch" href="//fonts.gstatic.com"> -->
    <!-- <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css"> -->

    <!-- Styles -->
    <!-- <link href="{{ asset('css/app.css') }}" rel="stylesheet"> -->

    <!-- <script src="{{ asset('js/jq.js') }}"></script> -->
    <script src="{{ asset('js/jquery-1.12.4.min.js') }}"></script>
    <script src="{{ asset('js/j2.js') }}"></script>
    <!-- <script src="{{ asset('js/news.js')}}"></script> -->
    <script src="{{ asset('js/jquery.easy_slides.js') }}"></script>

    <style>
        html {
            overflow: hidden!important;
        }
        .news-content {
            /* background: url('/img/els-bg.jpg'); */
            background-size: 100%;
            background-repeat: no-repeat;
            height: 100%;
        }
    </style>

    <title>ELS News</title>
</head>
<body>
    <div class="main">
        <div class="header-wrapper">
            <!-- <p>Enhancing Life's Standards</p> -->
        </div>
        <div class="time-wrapper">
            <div id="time"></div>
            <div id="date"></div>
        </div>

        <div class="board-wrapper">
            <div id="boardContent">
                <!-- <div class="slider slider_circle_10">
                </div> -->
            </div>
        </div>
        <div class="jo-wrapper">
            <div>
                <fieldset class="customer-name">
                    <legend>CUSTOMER</legend>
                    <span id="customerName">Jay The Great</span>
                </fieldset>
                <div id="itemsPlaceholder">No services selected</div>
                <table class="table" id="items">
                    <thead>
                        <tr>
                            <th class="text-left">NAME</th>
                            <th>PRICE/PC/KG</th>
                            <th>TOTAL</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr>
                            <td class="text-left">TOTAL</td>
                            <td></td>
                            <td id="totalPrice">
                                P <span></span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- <div class="news-wrapper">
            <div class="news-content" id="newsContent"></div>
        </div> -->

        <div id="announcement"></div>
    </div>
    <script id="newsTemplate" type="text/template">
        <div class="news-item">
            <div>
                <img class="thumbnail" src="@{{urlToImage}}" alt="">
            </div>
            <div class="content">
                <div class="title">@{{title}}</div>
                <div class="description">@{{description}}</div>
            </div>
            <hr>
        </div>
    </script>
    <script id="sliderTemplate" type="text/template">
        <div class="slider slider_circle_10">@{{slides}}</div>
    </script>
    <script id="slideTemplate" type="text/template">
        <div><img src="@{{source}}" alt=""></div>
    </script>
    <script id="videoTemplate" type="text/template">
        <video id="videoFile" muted="muted" controls type="video/mp4" src="@{{source}}" style="width: 100%" autoplay="true" loop></video>
    </script>
    <script id="itemTemplate" type="text/template">
        <tr>
            <td class="item-name">
                @{{quantity}}
                @{{name}}
            </td>
            <td>
                P @{{unit_price}}
            </td>
            <td>
                P @{{total_price}}
            </td>
        </tr>
    </script>
    <script id="announcementMarqueeOnTemplate" type="text/template">
        <marquee behavior="" direction="">
            <span>@{{announcementContent}}</span>
        </marquee>
    </script>
    <script id="announcementMarqueeOffTemplate" type="text/template">
        <div class="text-center">
            <span>@{{announcementContent}}</span>
        </div>
    </script>
    <!-- <v-app id="app">
        <news />
    </v-app> -->
</body>
</html>
