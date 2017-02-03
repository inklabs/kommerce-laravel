<?php
namespace App\Http\Controllers\Admin\Attribute;

use App\Http\Controllers\Controller;
use App\Lib\Arr;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Attribute\CreateAttributeValueCommand;
use inklabs\kommerce\Exception\EntityValidatorException;

class CreateAttributeValueController extends Controller
{
    public function post(Request $request)
    {
        $attributeId = $request->input('attributeId');

        $attributeValue = $request->input('attributeValue');
        $name        = $this->getStringOrNull(Arr::get($attributeValue, 'name'));
        $sortOrder   = $this->getIntOrNull(Arr::get($attributeValue, 'sortOrder'));
        $sku         = $this->getStringOrNull(Arr::get($attributeValue, 'sku'));
        $description = $this->getStringOrNull(Arr::get($attributeValue, 'description'));

        try {
            $this->dispatch(new CreateAttributeValueCommand(
                $name,
                $sortOrder,
                $sku,
                $description,
                $attributeId
            ));

            $this->flashSuccess('Successfully created attribute value');
        } catch (EntityValidatorException $e) {
            $this->flashError('Unable to create attribute value!');
            $this->flashFormErrors($e->getErrors());
        }

        return redirect()->route(
            'admin.attribute.attribute-values',
            [
                'attributeId' => $attributeId,
            ]
        );
    }
}
