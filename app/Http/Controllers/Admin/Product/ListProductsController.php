<?php
namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Product\ListProductsQuery;
use inklabs\kommerce\ActionResponse\Product\ListProductsResponse;

class ListProductsController extends Controller
{
    public function index(Request $httpRequest)
    {
        $queryString = $httpRequest->query('q');

        /** @var ListProductsResponse $response */
        $response = $this->dispatchQuery(new ListProductsQuery(
            $queryString,
            $this->getPaginationDTO(20)
        ));

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
