<form action="{{ $route }}" method="POST">
    {{ csrf_field() }}
    @if ($method === 'PUT')
        {{ method_field('PUT') }}
    @endif
    <label>
        Slug
        <input name="slug"
               type="text"
               id="zk-product-slug"
               placeholder="Slug"
               value="{{ $productDTO->slug or old('slug') }}">
    </label>

    <label>
        SKU
        <input name="sku"
               type="text"
               id="zk-product-sku"
               placeholder="SKU"
               value="{{ $productDTO->sku or old('sku') }}">
    </label>

    <label>
        Name
        <input name="name"
               type="text"
               id="zk-product-name"
               placeholder="Name"
               value="{{ $productDTO->name or old('name') }}">
    </label>

    <label>
        Description
        <textarea name="zk-product-description"
                  id="zk-product-description"
                  rows="3">{{ $productDTO->description or old('description') }}</textarea>
    </label>

    <label>
        Price
        <input name="price"
               type="number"
               id="zk-product-price"
               placeholder="Price"
               value="{{ $productDTO->unitPrice or old('price') }}">
    </label>
    <label>
        Quantity
        <input name="quantity"
               type="number"
               id="zk-product-quantity"
               placeholder="Quantity"
               value="{{ $productDTO->quantity or old('quantity') }}">
    </label>
    <fieldset>
        <legend>Inventory Settings</legend>
        <div>
            <input name="inventory-required"
                   type="checkbox"
                   id="zk-product-inventory-required"
                   @if (isset($productDTO->isInventoryRequired) && $productDTO->isInventoryRequired)
                        checked="checked"
                   @endif
            >
            <label for="zk-product-inventory-required">Inventory Required?</label>
        </div>
        <div>
            <input name="price-visible"
                   type="checkbox"
                   id="zk-product-price-visible"
                   @if (isset($productDTO->isPriceVisible) && $productDTO->isPriceVisible)
                        checked="checked"
                   @endif
            >
            <label for="zk-product-price-visible">Price Visible?</label>
        </div>
        <div>
            <input name="active"
                   type="checkbox"
                   id="zk-product-active"
                   @if (isset($productDTO->isActive) && $productDTO->isActive)
                        checked="checked"
                   @endif
            >
            <label for="zk-product-active">Active?</label>
        </div>
        <div>
            <input name="visible"
                   type="checkbox"
                   id="zk-product-visible"
                   @if (isset($productDTO->isVisible) && $productDTO->isVisible)
                        checked="checked"
                   @endif
            >
            <label for="zk-product-visible">Visible?</label>
        </div>
        <div>
            <input name="taxable"
                   type="checkbox"
                   id="zk-product-taxable"
                   @if (isset($productDTO->isTaxable) && $productDTO->isTaxable)
                        checked="checked"
                   @endif
            >
            <label for="zk-product-taxable">Taxable?</label>
        </div>
        <div>
            <input name="shippable"
                   type="checkbox"
                   id="zk-product-shippable"
                   @if (isset($productDTO->isShippable) && $productDTO->isShippable)
                        checked="checked"
                   @endif
            >
            <label for="zk-product-shippable">Shippable?</label>
        </div>

        <div>
            <input name="attachments-enabled"
                   type="checkbox"
                   id="zk-product-attachments-enabled"
                   @if (isset($productDTO->areAttachmentsEnabled) && $productDTO->areAttachmentsEnabled)
                        checked="checked"
                   @endif
            >
            <label for="zk-product-attachments-enabled">Attachments Enabled?</label>
        </div>
    </fieldset>

    <label>
        Shipping Weight
        <input name="shipping-weight"
               type="number"
               placeholder="Shipping Weight">
    </label>

    <label>
        Rating
        <input name="rating"
               type="number"
               placeholder="Rating">
    </label>

    <div>
        Default Image
        <label for="zk-product-default-image" class="button">Upload File</label>
        <input name="default-image"
               type="file"
               id="zk-product-default-image"
               class="show-for-sr">
    </div>

    <button type="submit" class="button">Submit</button>
</form>