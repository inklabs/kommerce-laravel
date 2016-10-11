<?php
namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Lib\Arr;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Product\UpdateProductCommand;
use inklabs\kommerce\EntityDTO\ProductDTO;
use inklabs\kommerce\Exception\EntityValidatorException;

class EditProductController extends Controller
{
    public function get($productId)
    {
        $product = $this->getProductWithAllData($productId);

        return $this->renderTemplate(
            'admin/product/edit.twig',
            [
                'product' => $product,
            ]
        );
    }

    public function post(Request $request)
    {
        $productId = $request->input('productId');
        $product = $this->getProductWithAllData($productId);

        $this->updateProductDTOFromPost($product, $request->input('product'));

        try {
            $this->dispatch(new UpdateProductCommand($product));

            $this->flashSuccess('Product has been saved.');
            return redirect()->route(
                'admin.product.edit',
                [
                    'productId' => $product->id->getHex(),
                ]
            );
        } catch (EntityValidatorException $e) {
            $this->flashError('Unable to save product!');
            $this->flashFormErrors($e->getErrors());
        }
    }

    private function updateProductDTOFromPost(ProductDTO & $productDTO, array $productValues)
    {
        $unitPrice = (int) floor(Arr::get($productValues, 'unitPrice') * 100);

        $productDTO->name = trim(Arr::get($productValues, 'name'));
        $productDTO->sku = trim(Arr::get($productValues, 'sku'));
        $productDTO->description = trim(Arr::get($productValues, 'description'));
        $productDTO->unitPrice = $unitPrice;
        $productDTO->quantity = Arr::get($productValues, 'quantity');
        $productDTO->shippingWeight = Arr::get($productValues, 'shippingWeight');
        $productDTO->isInventoryRequired = Arr::get($productValues, 'isInventoryRequired', false);
        $productDTO->isPriceVisible = Arr::get($productValues, 'isPriceVisible', false);
        $productDTO->isActive = Arr::get($productValues, 'isActive', false);
        $productDTO->isVisible = Arr::get($productValues, 'isVisible', false);
        $productDTO->isTaxable = Arr::get($productValues, 'isTaxable', false);
        $productDTO->isShippable = Arr::get($productValues, 'isShippable', false);
        $productDTO->isShippable = Arr::get($productValues, 'isShippable', false);
        $productDTO->areAttachmentsEnabled = Arr::get($productValues, 'areAttachmentsEnabled', false);
    }
}
