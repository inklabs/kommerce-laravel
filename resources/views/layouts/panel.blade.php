<!doctype html>
<html class="no-js" lang="">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title></title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" href="/favicon.ico">

    <!-- Place favicon.ico in the root directory -->

    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
</head>
<body>

    <!--[if lt IE 10]>
    <p class="browserupgrade">
        You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your
        browser</a> to improve your experience.</p>
    <![endif]-->

    @yield ('content')

    <script src="{{ asset('assets/js/vendor.js') }}"></script>
    <script>
        $(document).foundation();
    </script>
</body>
</html>