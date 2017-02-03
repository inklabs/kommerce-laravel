<?php
namespace App\Http\Controllers\Admin\Promotion\CartPriceRule;

use App\Http\Controllers\Controller;
use App\Lib\Arr;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\CartPriceRule\CreateCartPriceRuleProductItemCommand;
use inklabs\kommerce\Exception\EntityValidatorException;

class CreateCartPriceRuleProductItemController extends Controller
{
    public function post(Request $request)
    {
        $cartPriceRuleId = $request->input('cartPriceRuleId');
        $cartPriceRuleItem = $request->input('cartPriceRuleItem');
        $productId = Arr::get($cartPriceRuleItem, 'productId');
        $quantity = (int) Arr::get($cartPriceRuleItem, 'quantity');

        try {
            $this->dispatch(new CreateCartPriceRuleProductItemCommand(
                $cartPriceRuleId,
                $productId,
                $quantity
            ));

            $this->flashSuccess('Cart price rule product item has been created.');

        } catch (EntityValidatorException $e) {
            $this->flashError('Unable to create cart price rule product item!');
            $this->flashFormErrors($e->getErrors());
        }

        return redirect()->route(
            'admin.cart-price-rule.items',
            [
                'cartPriceRuleId' => $cartPriceRuleId,
            ]
        );
    }
}
