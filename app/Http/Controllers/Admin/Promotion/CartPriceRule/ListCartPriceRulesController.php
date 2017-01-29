<?php
namespace App\Http\Controllers\Admin\Promotion\CartPriceRule;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\CartPriceRule\ListCartPriceRulesQuery;
use inklabs\kommerce\Action\CartPriceRule\Query\ListCartPriceRulesRequest;
use inklabs\kommerce\Action\CartPriceRule\Query\ListCartPriceRulesResponse;

class ListCartPriceRulesController extends Controller
{
    public function index(Request $httpRequest)
    {
        $queryString = $httpRequest->query('q');

        $request = new ListCartPriceRulesRequest(
            $queryString,
            $this->getPaginationDTO(20)
        );

        $response = new ListCartPriceRulesResponse();
        $this->dispatchQuery(new ListCartPriceRulesQuery($request, $response));

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
