<?php
namespace App\Http\Controllers\Admin\Attribute;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Attribute\DeleteProductAttributeCommand;
use inklabs\kommerce\Exception\EntityValidatorException;

class DeleteProductAttributeController extends Controller
{
    public function post(Request $request)
    {
        $attributeValueId = $request->input('attributeValueId');
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
            'admin.attribute.attribute-value.product-attributes',
            [
                'attributeValueId' => $attributeValueId,
            ]
        );
    }
}
