<?php
namespace App\Http\Controllers\Admin\Promotion\CartPriceRule;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\CartPriceRule\DeleteCartPriceRuleDiscountCommand;
use inklabs\kommerce\Exception\EntityValidatorException;

class DeleteCartPriceRuleDiscountController extends Controller
{
    public function post(Request $request)
    {
        $cartPriceRuleId = $request->input('cartPriceRuleId');
        $cartPriceRuleDiscountId = $request->input('cartPriceRuleDiscountId');

        try {
            $this->dispatch(new DeleteCartPriceRuleDiscountCommand($cartPriceRuleDiscountId));

            $this->flashSuccess('Cart price rule discount has been removed.');

        } catch (EntityValidatorException $e) {
            $this->flashError('Unable to remove cart price rule discount!');
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
