<?php
namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;

class ListProductTagsController extends Controller
{
    public function index($productId)
    {
        $product = $this->getProductWithAllData($productId);

        return $this->renderTemplate(
            '@theme/admin/product/tags.twig',
            [
                'product' => $product,
            ]
        );
    }
}
