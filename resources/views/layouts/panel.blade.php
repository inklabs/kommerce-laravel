<!doctype html>
<html class="no-js" lang="">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Laravel Kommerce</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" href="/favicon.ico">

    <!-- Place favicon.ico in the root directory -->

    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
</head>
<body>

<div class="zk-main-wrapper">
    <aside class="zk-main-sidebar">

        <div class="zk-sidebar-branding">
            <img class="zk-sidebar-logo" src="//placehold.it/150x150" alt="">
        </div>

        <ul class="zk-menu vertical menu" data-accordion-menu>
            <li>
                <a href="#">Product</a>
                <ul class="vertical menu">
                    <li><a href="{{ route('p.index') }}">List All Products</a></li>
                    <li><a href="{{ route('p.createDummy') }}">Create Dummy Product</a></li>
                </ul>
            </li>
            <li><a href="#2">Item 2</a></li>
            <li><a href="#3">Item 3</a></li>
            <li><a href="#4">Item 4</a></li>
        </ul>
    </aside>
    <section class="zk-main-content">
        @yield ('content')
    </section>
</div>

<script src="{{ asset('assets/js/vendor.js') }}"></script>
<script>
    $(document).foundation();
</script>
</body>
</html>