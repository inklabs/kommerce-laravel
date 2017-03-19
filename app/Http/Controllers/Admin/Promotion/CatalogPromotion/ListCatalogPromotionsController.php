<?php
namespace App\Http\Controllers\Admin\Promotion\CatalogPromotion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\CatalogPromotion\ListCatalogPromotionsQuery;
use inklabs\kommerce\ActionResponse\CatalogPromotion\ListCatalogPromotionsResponse;

class ListCatalogPromotionsController extends Controller
{
    public function index(Request $httpRequest)
    {
        $queryString = $httpRequest->query('q');

        /** @var ListCatalogPromotionsResponse $response */
        $response = $this->dispatchQuery(new ListCatalogPromotionsQuery(
            $queryString,
            $this->getPaginationDTO(20)
        ));

        $catalogPromotions = $response->getCatalogPromotionDTOsWithAllData();
        $pagination = $response->getPaginationDTO();

        return $this->renderTemplate(
            '@admin/promotion/catalog-promotion/index.twig',
            [
                'catalogPromotions' => $catalogPromotions,
                'pagination' => $pagination,
            ]
        );
    }
}
