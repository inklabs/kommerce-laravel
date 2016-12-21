<?php
namespace App\Http\Controllers\Admin\Promotion\CartPriceRule;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ListCartPriceRulesController extends Controller
{
    public function index(Request $httpRequest)
    {
        $cartPriceRules = [];
        $pagination = null;

        return $this->renderTemplate(
            '@theme/admin/promotion/cart-price-rule/index.twig',
            [
                'cartPriceRules' => $cartPriceRules,
                'pagination' => $pagination,
            ]
        );
    }
}
