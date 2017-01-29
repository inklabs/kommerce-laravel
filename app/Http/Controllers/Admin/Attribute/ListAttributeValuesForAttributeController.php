<?php
namespace App\Http\Controllers\Admin\Attribute;

use App\Http\Controllers\Controller;

class ListAttributeValuesForAttributeController extends Controller
{
    public function get($attributeId)
    {
        $attribute = $this->getAttributeWithAllData($attributeId);

        return $this->renderTemplate(
            '@admin/attribute/attribute-values.twig',
            [
                'attribute' => $attribute,
            ]
        );
    }
}
