<?php
namespace App\Http\Controllers\Admin\Attribute;

use App\Http\Controllers\Controller;
use App\Lib\Arr;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Attribute\UpdateAttributeCommand;
use inklabs\kommerce\Entity\AttributeChoiceType;
use inklabs\kommerce\Exception\EntityValidatorException;

class EditAttributeController extends Controller
{
    public function get($attributeId)
    {
        $attribute = $this->getAttributeWithAllData($attributeId);

        return $this->renderTemplate(
            '@theme/admin/attribute/edit.twig',
            [
                'attribute' => $attribute,
                'validAttributeChoiceTypeMap' => AttributeChoiceType::getNameMap(),
            ]
        );
    }

    public function post(Request $request)
    {
        $attributeId = $request->input('attributeId');
        $attributeValues = $request->input('attribute');

        $name = trim(Arr::get($attributeValues, 'name'));
        $choiceTypeSlug = AttributeChoiceType::createById(Arr::get($attributeValues, 'choiceType'))->getSlug();
        $sortOrder = (int) Arr::get($attributeValues, 'sortOrder');
        $description = trim(Arr::get($attributeValues, 'description'));

        try {
            $this->dispatch(new UpdateAttributeCommand(
                $name,
                $choiceTypeSlug,
                $sortOrder,
                $description,
                $attributeId
            ));

            $this->flashSuccess('Attribute has been saved.');
            return redirect()->route(
                'admin.attribute.edit',
                [
                    'attributeId' => $attributeId,
                ]
            );
        } catch (EntityValidatorException $e) {
            $this->flashError('Unable to save attribute!');
            $this->flashFormErrors($e->getErrors());
        }

        return $this->get($attributeId);
    }
}
