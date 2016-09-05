<?php
namespace App\Http\Controllers\Admin\Promotion\CatalogPromotion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ListCatalogPromotionsController extends Controller
{
    public function index(Request $httpRequest)
    {
        $catalogPromotions = [];
        $pagination = null;

        return $this->renderTemplate(
            'admin/promotion/catalog-promotion/index.twig',
            [
                'catalogPromotions' => $catalogPromotions,
                'pagination' => $pagination,
            ]
        );
    }
}
