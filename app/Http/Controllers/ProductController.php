<?php
namespace App\Http\Controllers;

class ProductController extends Controller
{
    public function show($slug, $productId)
    {
        $cartDTO = $this->getCart();
        $productDTO = $this->getProductWithAllData($productId);

        if ($slug !== $productDTO->slug) {
            return redirect()->route(
                'product.show',
                [
                    'slug' => $productDTO->slug,
                    'productId' => $productDTO->id->getHex(),
                ]
            );
        }

        return $this->renderTemplate(
            '@store/product/show.twig',
            [
                'cart' => $cartDTO,
                'product' => $productDTO,
                'relatedProducts' => $this->getRandomProducts(4),
            ]
        );
    }
}
