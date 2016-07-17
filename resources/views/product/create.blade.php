@extends ('layouts.panel')

@section ('content')

    @include ('product.partials.form', [
        'method' => 'POST',
        'route' => route('p.store')
    ])

@endsection