@extends ('layouts.panel')

@section ('content')


    <div class="row">
        <div class="column">
            <h1>
                {{ $productDTO->name }}
            </h1>

            <p>
                <strong>Slug:</strong>
                {{ $productDTO->slug }}
            </p>

            <p>
                <strong>Quantity:</strong>
                {{ $productDTO->quantity }}
            </p>

            <p>
                <strong>SKU:</strong>
                {{ $productDTO->sku }}
            </p>

            <p>
                <strong>Is Inventory Required: </strong>
                @if ($productDTO->isInventoryRequired)
                    True
                @else
                    False
                @endif
            </p>

            <p>
                <strong>Is Active: </strong>
                @if ($productDTO->isActive)
                    True
                @else
                    False
                @endif
            </p>

            <p>
                <strong>Is Inventory Required: </strong>
                @if ($productDTO->isInventoryRequired)
                    True
                @else
                    False
                @endif
            </p>

            <p>
                <strong>Is Price Visible</strong>
                @if ($productDTO->isPriceVisible)
                    True
                @else
                    False
                @endif
            </p>

            <p>
                <strong>Is Active</strong>
                @if ($productDTO->isActive)
                    True
                @else
                    False
                @endif
            </p>

            <p>
                <strong>Is Visible</strong>
                @if ($productDTO->isVisible)
                    True
                @else
                    False
                @endif
            </p>

            <p>
                <strong>Is Taxable</strong>
                @if ($productDTO->isTaxable)
                    True
                @else
                    False
                @endif
            </p>

            <p>
                <strong>Is Shippable</strong>
                @if ($productDTO->isShippable)
                    True
                @else
                    False
                @endif
            </p>

            <p>
                <strong>Is In Stock</strong>
                @if ($productDTO->isInStock)
                    True
                @else
                    False
                @endif
            </p>

            <p>
                <strong>Are Attachments Enabled</strong>
                @if ($productDTO->areAttachmentsEnabled)
                    True
                @else
                    False
                @endif
            </p>

            <a href="{{ route('p.edit', $productDTO->id) }}" class="button">Edit Product</a>
        </div>
    </div>

@endsection