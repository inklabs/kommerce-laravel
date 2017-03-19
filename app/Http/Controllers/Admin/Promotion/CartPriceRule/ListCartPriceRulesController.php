<?php
namespace App\Http\Controllers\Admin\Promotion\CartPriceRule;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\CartPriceRule\ListCartPriceRulesQuery;
use inklabs\kommerce\ActionResponse\CartPriceRule\ListCartPriceRulesResponse;

class ListCartPriceRulesController extends Controller
{
    public function index(Request $httpRequest)
    {
        $queryString = $httpRequest->query('q');

        /** @var ListCartPriceRulesResponse $response */
        $response = $this->dispatchQuery(new ListCartPriceRulesQuery(
            $queryString,
            $this->getPaginationDTO(20)
        ));

        $cartPriceRules = $response->getCartPriceRuleDTOs();
        $pagination = $response->getPaginationDTO();

        return $this->renderTemplate(
            '@admin/promotion/cart-price-rule/index.twig',
            [
                'cartPriceRules' => $cartPriceRules,
                'pagination' => $pagination,
                'queryString' => $queryString,
            ]
        );
    }
}
