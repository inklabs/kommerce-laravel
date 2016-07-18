@extends ('layouts.panel')

@section ('content')

    <article>
        <div class="row lk-list-product-header hide-for-small-only">
            @foreach ($show as $name => $true)
                @if ($true)
                    <div class="medium-3 columns">
                        <span class="lk-list-product-title">{{ $name }}</span>
                    </div>
                @endif
            @endforeach
        </div>
        @foreach ($productDTOs as $product)
            <a href="{{ route('p.show', $product->id) }}" class="lk-list-product-link">
                <div class="row">
                    @foreach ($show as $name => $true)
                        @if ($true)
                            <div class="medium-3 columns">
                                <span class="lk-list-product-title show-for-small-only">{{ $name }}</span>
                                <span>{{ $product->{$name} }}</span>
                            </div>
                        @endif
                    @endforeach
                </div>
            </a>
        @endforeach
    </article>

@endsection