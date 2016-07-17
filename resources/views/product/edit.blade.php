@extends ('layouts.panel')

@section ('content')

    @include ('product.partials.form', [
        'method' => 'PUT',
        'route' => route('p.update', $productDTO->id)
    ])

@endsection