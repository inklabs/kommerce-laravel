<?php
namespace App\Http\Controllers\Admin\Attribute;

use App\Http\Controllers\Controller;

class ListProductAttributesForAttributeController extends Controller
{
    public function get($attributeId)
    {
        $attribute = $this->getAttributeWithAllData($attributeId);

        return $this->renderTemplate(
            '@theme/admin/attribute/product-attributes.twig',
            [
                'attribute' => $attribute,
            ]
        );
    }
}
