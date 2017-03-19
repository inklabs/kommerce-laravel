<?php
namespace App\Http\Controllers\Admin\Promotion\CartPriceRule;

use App\Http\Controllers\Controller;

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
