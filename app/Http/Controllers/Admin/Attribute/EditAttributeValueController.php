<?php
namespace App\Http\Controllers\Admin\Attribute;

use App\Http\Controllers\Controller;
use App\Lib\Arr;
use Illuminate\Http\Request;
use inklabs\kommerce\Exception\EntityValidatorException;

class EditAttributeValueController extends Controller
{
    public function get($attributeValueId)
    {
        $attributeValue = $this->getAttributeValueWithAllData($attributeValueId);

        return $this->renderTemplate(
            '@theme/admin/attribute/attribute-value/edit.twig',
            [
                'attributeValue' => $attributeValue,
            ]
        );
    }

//    public function post(Request $request)
//    {
//        $attributeValueId = $request->input('attributeValueId');
//        $attributeValueValues = $request->input('attributeValue');
//
//        $name = trim(Arr::get($attributeValueValues, 'name'));
//        $sortOrder = (int) Arr::get($attributeValueValues, 'sortOrder');
//        $description = trim(Arr::get($attributeValueValues, 'description'));
//
//        try {
//            $this->dispatch(new UpdateAttributeCommand(
//                $name,
//                $sortOrder,
//                $description,
//                $attributeValueId
//            ));
//
//            $this->flashSuccess('Attribute has been saved.');
//            return redirect()->route(
//                'admin.attributeValue.edit',
//                [
//                    'attributeValueId' => $attributeValueId,
//                ]
//            );
//        } catch (EntityValidatorException $e) {
//            $this->flashError('Unable to save attributeValue!');
//            $this->flashFormErrors($e->getErrors());
//        }
//
//        return $this->get($attributeValueId);
//    }
}
