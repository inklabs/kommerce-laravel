<?php
namespace App\Http\Controllers;

use App\Http\Requests;
use inklabs\kommerce\Action\Product\GetProductQuery;
use inklabs\kommerce\Action\Product\Query\GetProductRequest;
use inklabs\kommerce\Action\Product\Query\GetProductResponse;

class ProductController extends Controller
{
    /**
     * @param string $slug
     * @param string $productId
     * @return \Illuminate\Contracts\View\Factory | \Illuminate\View\View
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

        return view(
            'product/show',
            [
                'productDTO' => $productDTO,
            ]
        );
    }
}
