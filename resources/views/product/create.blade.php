@extends ('layouts.panel')

@section ('content')

    <div class="row">
        <div class="column">
            @include ('product.partials.form', [
                'method' => 'POST',
                'route' => route('p.store')
            ])
        </div>
    </div>

@endsection