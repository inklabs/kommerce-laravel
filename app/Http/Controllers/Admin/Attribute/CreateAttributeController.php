<?php
namespace App\Http\Controllers\Admin\Attribute;

use App\Http\Controllers\Controller;
use App\Lib\Arr;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Attribute\CreateAttributeCommand;
use inklabs\kommerce\Entity\AttributeChoiceType;
use inklabs\kommerce\Exception\EntityValidatorException;

class CreateAttributeController extends Controller
{
    public function get()
    {
        return $this->renderTemplate(
            '@theme/admin/attribute/new.twig',
            [
                'validAttributeChoiceTypeMap' => AttributeChoiceType::getNameMap(),
            ]
        );
    }

    public function post(Request $request)
    {
        $attributeValues = $request->input('attribute');

        $name = trim(Arr::get($attributeValues, 'name'));
        $choiceTypeSlug = AttributeChoiceType::createById(Arr::get($attributeValues, 'choiceType'))->getSlug();
        $sortOrder = (int) Arr::get($attributeValues, 'sortOrder');
        $description = trim(Arr::get($attributeValues, 'description'));

        try {
            $command = new CreateAttributeCommand(
                $name,
                $choiceTypeSlug,
                $sortOrder,
                $description
            );

            $this->dispatch($command);

            $this->flashSuccess('Attribute has been created.');
            return redirect()->route(
                'admin.attribute.edit',
                [
                    'attributeId' => $command->getAttributeId()->getHex(),
                ]
            );
        } catch (EntityValidatorException $e) {
            $this->flashError('Unable to create attribute!');
            $this->flashFormErrors($e->getErrors());
        }

        return $this->get();
    }
}
