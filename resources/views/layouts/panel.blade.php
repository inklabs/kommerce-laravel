<!doctype html>
<html class="no-js" lang="">
<head>
    @include ('layouts.content.head')
</head>
<body>

    <header class="lk-topbar">
        @include ('layouts.content.topbar')
    </header>

    <div class="lk-wrapper">
        <nav class="lk-sidebar">
            @include ('layouts.content.sidebar')
        </nav>
        <section class="lk-content">
            <div id="lk-app"></div>
        </section>
    </div>

    <script src="{{ asset('assets/common.js') }}"></script>
    <script src="{{ asset('assets/app.js') }}"></script>
    <script src="{{ asset('assets/package.js') }}"></script>

</body>
</html>
