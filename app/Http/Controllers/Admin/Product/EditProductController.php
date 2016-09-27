<?php
namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EditProductController extends Controller
{
    public function index($productId)
    {
        $product = $this->getProductWithAllData($productId);

        return $this->renderTemplate(
            'admin/product/edit.twig',
            [
                'product' => $product,
            ]
        );
    }
}
