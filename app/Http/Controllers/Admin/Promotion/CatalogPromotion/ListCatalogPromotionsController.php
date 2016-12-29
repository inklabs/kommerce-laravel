<?php
namespace App\Http\Controllers\Admin\Promotion\CatalogPromotion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\CatalogPromotion\ListCatalogPromotionsQuery;
use inklabs\kommerce\Action\CatalogPromotion\Query\ListCatalogPromotionsRequest;
use inklabs\kommerce\Action\CatalogPromotion\Query\ListCatalogPromotionsResponse;

class ListCatalogPromotionsController extends Controller
{
    public function index(Request $httpRequest)
    {
        $queryString = $httpRequest->query('q');

        $request = new ListCatalogPromotionsRequest(
            $queryString,
            $this->getPaginationDTO(20)
        );

        $response = new ListCatalogPromotionsResponse();
        $this->dispatchQuery(new ListCatalogPromotionsQuery($request, $response));

        $catalogPromotions = $response->getCatalogPromotionDTOsWithAllData();
        $pagination = $response->getPaginationDTO();

        return $this->renderTemplate(
            '@theme/admin/promotion/catalog-promotion/index.twig',
            [
                'catalogPromotions' => $catalogPromotions,
                'pagination' => $pagination,
            ]
        );
    }
}
