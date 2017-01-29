<?php
namespace App\Http\Controllers\Admin\Promotion\CartPriceRule;

use App\Http\Controllers\Controller;
use inklabs\kommerce\Action\TaxRate\ListTaxRatesQuery;
use inklabs\kommerce\Action\TaxRate\Query\ListTaxRatesRequest;
use inklabs\kommerce\Action\TaxRate\Query\ListTaxRatesResponse;

class ListCartPriceRuleItemsController extends Controller
{
    public function index($cartPriceRuleId)
    {
        $cartPriceRule = $this->getCartPriceRuleWithAllData($cartPriceRuleId);

        return $this->renderTemplate(
            '@admin/promotion/cart-price-rule/items.twig',
            [
                'cartPriceRule' => $cartPriceRule,
            ]
        );
    }
}
