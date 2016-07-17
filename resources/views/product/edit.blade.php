@extends ('layouts.panel')

@section ('content')

    {{--Create Product--}}
    {{--+defaultImage: null--}}
    {{--+tags: []--}}
    {{--+images: []--}}
    {{--+tagImages: []--}}
    {{--+options: []--}}
    {{--+textOptions: []--}}
    {{--+productQuantityDiscounts: []--}}
    {{--+optionProducts: []--}}
    {{--+productAttributes: []--}}
    {{--+price: null--}}
    {{--+id: null--}}
    {{--+created: null--}}
    {{--+updated: null--}}
    {{--+createdFormatted: null--}}
    {{--+updatedFormatted: null--}}

    <div class="row">
        <div class="column">
            @include ('product.partials.form', [
                'method' => 'PUT',
                'route' => route('p.update', $productDTO->id)
            ])
        </div>
    </div>

@endsection