<?php
namespace App\Http\Controllers\Admin\Promotion\CartPriceRule;

use App\Http\Controllers\Controller;
use App\Lib\Arr;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\CartPriceRule\CreateCartPriceRuleDiscountCommand;
use inklabs\kommerce\Exception\EntityValidatorException;

class CreateCartPriceRuleDiscountController extends Controller
{
    public function post(Request $request)
    {
        $cartPriceRuleId = $request->input('cartPriceRuleId');
        $cartPriceRuleDiscount = $request->input('cartPriceRuleDiscount');
        $productId = Arr::get($cartPriceRuleDiscount, 'productId');
        $quantity = (int) Arr::get($cartPriceRuleDiscount, 'quantity');

        try {
            $this->dispatch(new CreateCartPriceRuleDiscountCommand(
                $cartPriceRuleId,
                $productId,
                $quantity
            ));

            $this->flashSuccess('Cart price rule discount has been created.');

        } catch (EntityValidatorException $e) {
            $this->flashError('Unable to create cart price rule discount!');
            $this->flashFormErrors($e->getErrors());
        }

        return redirect()->route(
            'admin.cart-price-rule.discounts',
            [
                'cartPriceRuleId' => $cartPriceRuleId,
            ]
        );
    }
}
