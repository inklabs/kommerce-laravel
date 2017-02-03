<?php
namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Product\ListProductsQuery;
use inklabs\kommerce\Action\Product\Query\ListProductsRequest;
use inklabs\kommerce\Action\Product\Query\ListProductsResponse;

class ListProductsController extends Controller
{
    public function index(Request $httpRequest)
    {
        $queryString = $httpRequest->query('q');

        $request = new ListProductsRequest(
            $queryString,
            $this->getPaginationDTO(20)
        );

        $response = new ListProductsResponse();
        $this->dispatchQuery(new ListProductsQuery($request, $response));

        $products = $response->getProductDTOs();
        $pagination = $response->getPaginationDTO();

        return $this->renderTemplate(
            '@admin/product/index.twig',
            [
                'products' => $products,
                'pagination' => $pagination,
                'queryString' => $queryString,
            ]
        );
    }
}
