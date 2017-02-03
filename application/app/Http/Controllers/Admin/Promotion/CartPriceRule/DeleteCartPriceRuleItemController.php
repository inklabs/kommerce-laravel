<?php
namespace App\Http\Controllers\Admin\Promotion\CartPriceRule;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\CartPriceRule\DeleteCartPriceRuleItemCommand;
use inklabs\kommerce\Exception\EntityValidatorException;

class DeleteCartPriceRuleItemController extends Controller
{
    public function post(Request $request)
    {
        $cartPriceRuleId = $request->input('cartPriceRuleId');
        $cartPriceRuleItemId = $request->input('cartPriceRuleItemId');

        try {
            $this->dispatch(new DeleteCartPriceRuleItemCommand($cartPriceRuleItemId));

            $this->flashSuccess('Cart price rule product item has been removed.');

        } catch (EntityValidatorException $e) {
            $this->flashError('Unable to remove cart price rule product item!');
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
