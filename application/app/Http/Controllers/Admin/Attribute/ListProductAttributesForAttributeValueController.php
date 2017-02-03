<?php
namespace App\Http\Controllers\Admin\Attribute;

use App\Http\Controllers\Controller;
use inklabs\kommerce\EntityDTO\AttributeValueDTO;

class ListProductAttributesForAttributeValueController extends Controller
{
    public function get($attributeValueId)
    {
        $attributeValue = $this->getAttributeValueWithAllData($attributeValueId);

        return $this->renderTemplate(
            '@admin/attribute/attribute-value/product-attributes.twig',
            [
                'attributeValue' => $attributeValue,
                'products' => $this->getAttributeValueProducts($attributeValue),
            ]
        );
    }

    private function getAttributeValueProducts(AttributeValueDTO $attributeValueDTO)
    {
        foreach ($attributeValueDTO->productAttributes as $productAttribute) {
            yield $productAttribute->product;
        }

    }
}
