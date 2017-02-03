<?php
namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Attribute\DeleteProductAttributeCommand;
use inklabs\kommerce\Exception\EntityValidatorException;

class RemoveAttributeValueFromProductController extends Controller
{
    public function post(Request $request)
    {
        $productId = $request->input('productId');
        $productAttributeId = $request->input('productAttributeId');

        try {
            $command = new DeleteProductAttributeCommand($productAttributeId);

            $this->dispatch($command);

            $this->flashSuccess('Product has been removed.');
        } catch (EntityValidatorException $e) {
            $this->flashError('Unable to remove product!');
            $this->flashFormErrors($e->getErrors());
        }

        return redirect()->route(
            'admin.product.attributes',
            [
                'productId' => $productId,
            ]
        );
    }
}
