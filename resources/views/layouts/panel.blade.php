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
            @yield ('content')
        </section>
    </div>

<script src="{{ asset('assets/js/vendor.js') }}"></script>
<script>
    $(document).foundation();
</script>
</body>
</html>
