<?php
namespace App\Http\Controllers\Admin\Attribute;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Attribute\CreateProductAttributeCommand;
use inklabs\kommerce\Exception\EntityValidatorException;

class CreateProductAttributeController extends Controller
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

            $this->flashSuccess('Product has been added.');
        } catch (EntityValidatorException $e) {
            $this->flashError('Unable to add product!');
            $this->flashFormErrors($e->getErrors());
        }

        return redirect()->route(
            'admin.attribute.attribute-value.product-attributes',
            [
                'attributeValueId' => $attributeValueId,
            ]
        );
    }
}
