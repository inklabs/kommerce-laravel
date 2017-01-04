<?php
namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Attribute\CreateProductAttributeCommand;
use inklabs\kommerce\Exception\EntityValidatorException;

class AddAttributeValueToProductController extends Controller
{
    public function post(Request $request)
    {
        $attributeValueId = $request->input('attributeValueId');
        $productId = $request->input('productId');

        try {
            $command = new CreateProductAttributeCommand(
                $attributeValueId,
                $productId
            );

            $this->dispatch($command);

            $this->flashSuccess('Attribute value has been added.');
        } catch (EntityValidatorException $e) {
            $this->flashError('Unable to add attribute value!');
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
