<?php
namespace App\Http\Controllers;

use App\Http\Requests;
use inklabs\kommerce\Action\Product\GetProductQuery;
use inklabs\kommerce\Action\Product\GetRandomProductsQuery;
use inklabs\kommerce\Action\Product\Query\GetProductRequest;
use inklabs\kommerce\Action\Product\Query\GetProductResponse;
use inklabs\kommerce\Action\Product\Query\GetRandomProductsRequest;
use inklabs\kommerce\Action\Product\Query\GetRandomProductsResponse;

class ProductController extends Controller
{
    /**
     * @param string $slug
     * @param string $productId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function show($slug, $productId)
    {
        $request = new GetProductRequest($productId);
        $response = new GetProductResponse($this->getPricing());
        $this->dispatchQuery(new GetProductQuery($request, $response));

        $productDTO = $response->getProductDTOWithAllData();

        if ($slug !== $productDTO->slug) {
            return redirect()->route(
                'product.show',
                [
                    'slug' => $productDTO->slug,
                    'productId' => $productDTO->id->getHex(),
                ]
            );
        }

        $request = new GetRandomProductsRequest(4);
        $response = new GetRandomProductsResponse($this->getPricing());
        $this->dispatchQuery(new GetRandomProductsQuery($request, $response));
        $relatedProductDTOs = $response->getProductDTOs();

        return $this->renderTemplate(
            'product/show.twig',
            [
                'product' => $productDTO,
                'relatedProducts' => $relatedProductDTOs,
            ]
        );
    }
}
