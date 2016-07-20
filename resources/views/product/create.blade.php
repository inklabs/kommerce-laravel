@extends ('panel')

@section ('content')

    <article>
        @include ('product.partials.form', [
            'method' => 'POST',
            'route' => route('p.store')
        ])
    </article>

@endsection