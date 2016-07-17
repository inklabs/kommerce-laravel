@extends ('layouts.panel')

@section ('content')

    <div>
        <div class="row">
            @foreach ($show as $name => $true)
                @if ($true)
                    <div class="small-3 columns">{{ $name }}</div>
                @endif
            @endforeach
        </div>
        @foreach ($productDTOs as $product)
            <a href="{{ route('p.show', $product->id) }}" style="display:block;">
                <div class="row">
                    @foreach ($show as $name => $true)
                        @if ($true)
                            <div class="small-3 columns">{{ $product->{$name} }}</div>
                        @endif
                    @endforeach
                </div>
            </a>
        @endforeach
    </div>

@endsection