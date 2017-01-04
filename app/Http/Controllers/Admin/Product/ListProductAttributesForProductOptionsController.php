<?php
namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;

class ListProductAttributesForProductOptionsController extends Controller
{
    public function index($productId)
    {
        $product = $this->getProductWithAllData($productId);

        return $this->renderTemplate(
            '@theme/admin/product/attributes.twig',
            [
                'product' => $product,
            ]
        );
    }
}
