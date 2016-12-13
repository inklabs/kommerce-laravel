<?php
namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;

class ListProductOptionsController extends Controller
{
    public function index($productId)
    {
        $product = $this->getProductWithAllData($productId);

        return $this->renderTemplate(
            'admin/product/options.twig',
            [
                'product' => $product,
            ]
        );
    }
}
