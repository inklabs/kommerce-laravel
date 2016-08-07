<!doctype html>
<html class="no-js" lang="">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Larakommerce</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" id="token" content="{{ csrf_token() }}">

    <!-- Place favicon.ico in the root directory -->
    <link rel="icon" href="/favicon.ico">

    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">

</head>
<body>

    <header class="lk-topbar">
        <div class="top-bar">
            <div class="top-bar-title">
            <span data-responsive-toggle="responsive-menu" data-hide-for="medium">
              <button class="menu-icon dark" type="button" data-toggle></button>
            </span>
                <strong>Larakommerce</strong>
            </div>
            <div id="responsive-menu">
                <div class="top-bar-left">
                    <ul class="menu">
                        <li><a href="#">Two</a></li>
                        <li><a href="#">Three</a></li>
                    </ul>
                </div>
                <div class="top-bar-right">
                    <ul class="menu">
                        {{--<li><input type="search" placeholder="Search"></li>--}}
                        {{--<li><button type="button" class="button">Search</button></li>--}}
                    </ul>
                </div>
            </div>
        </div>
    </header>

    <div id="content">
        @yield('content')
    </div>

    <footer>
        <h3>Demo Data Setup:</h3>
        <ul>
            <li><a href="dummy-data/create-dummy-product">Create Dummy Product and Tag</a></li>
            <li><a href="dummy-data/create-dummy-catalog-price-rule">Create Dummy Catalog Price Rule</a></li>
        </ul>
    </footer>

<script src="{{ asset('assets/main.js') }}"></script>

</body>
</html>
