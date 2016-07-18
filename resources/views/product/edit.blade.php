@extends ('layouts.panel')

@section ('content')

    <article>
        @include ('product.partials.form', [
            'method' => 'PUT',
            'route' => route('p.update', $productDTO->id)
        ])
    </article>

@endsection