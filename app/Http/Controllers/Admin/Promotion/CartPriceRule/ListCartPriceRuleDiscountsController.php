<?php
namespace App\Http\Controllers\Admin\Promotion\CartPriceRule;

use App\Http\Controllers\Controller;
use inklabs\kommerce\Action\TaxRate\ListTaxRatesQuery;
use inklabs\kommerce\Action\TaxRate\Query\ListTaxRatesRequest;
use inklabs\kommerce\Action\TaxRate\Query\ListTaxRatesResponse;

class ListCartPriceRuleDiscountsController extends Controller
{
    public function index($cartPriceRuleId)
    {
        $cartPriceRule = $this->getCartPriceRuleWithAllData($cartPriceRuleId);

        return $this->renderTemplate(
            '@theme/admin/promotion/cart-price-rule/discounts.twig',
            [
                'cartPriceRule' => $cartPriceRule,
            ]
        );
    }
}
