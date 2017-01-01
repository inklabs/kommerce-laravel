<?php
namespace App\Http\Controllers\Admin\Attribute;

use App\Http\Controllers\Controller;

class ListProductAttributesForAttributeValueController extends Controller
{
    public function get($attributeValueId)
    {
        $attributeValue = $this->getAttributeValueWithAllData($attributeValueId);

        return $this->renderTemplate(
            '@theme/admin/attribute/attribute-value/product-attributes.twig',
            [
                'attributeValue' => $attributeValue,
            ]
        );
    }
}
