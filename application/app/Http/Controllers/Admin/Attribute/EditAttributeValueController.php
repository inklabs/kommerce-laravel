<?php
namespace App\Http\Controllers\Admin\Attribute;

use App\Http\Controllers\Controller;
use App\Lib\Arr;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Attribute\UpdateAttributeValueCommand;
use inklabs\kommerce\Exception\EntityValidatorException;

class EditAttributeValueController extends Controller
{
    public function get($attributeValueId)
    {
        $attributeValue = $this->getAttributeValueWithAllData($attributeValueId);

        return $this->renderTemplate(
            '@admin/attribute/attribute-value/edit.twig',
            [
                'attributeValue' => $attributeValue,
            ]
        );
    }

    public function post(Request $request)
    {
        $attributeValueId = $request->input('attributeValueId');

        $attributeValue = $request->input('attributeValue');
        $name        = $this->getStringOrNull(Arr::get($attributeValue, 'name'));
        $sortOrder   = $this->getIntOrNull(Arr::get($attributeValue, 'sortOrder'));
        $sku         = $this->getStringOrNull(Arr::get($attributeValue, 'sku'));
        $description = $this->getStringOrNull(Arr::get($attributeValue, 'description'));

        try {
            $this->dispatch(new UpdateAttributeValueCommand(
                $name,
                $sortOrder,
                $sku,
                $description,
                $attributeValueId
            ));

            $this->flashSuccess('Attribute value has been saved.');
            return redirect()->route(
                'admin.attribute.attribute-value.edit',
                [
                    'attributeValueId' => $attributeValueId,
                ]
            );
        } catch (EntityValidatorException $e) {
            $this->flashError('Unable to save attribute value!');
            $this->flashFormErrors($e->getErrors());
        }

        return $this->get($attributeValueId);
    }
}
