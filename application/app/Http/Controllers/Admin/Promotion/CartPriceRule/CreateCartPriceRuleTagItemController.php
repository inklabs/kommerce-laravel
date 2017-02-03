<?php
namespace App\Http\Controllers\Admin\Promotion\CartPriceRule;

use App\Http\Controllers\Controller;
use App\Lib\Arr;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\CartPriceRule\CreateCartPriceRuleTagItemCommand;
use inklabs\kommerce\Exception\EntityValidatorException;

class CreateCartPriceRuleTagItemController extends Controller
{
    public function post(Request $request)
    {
        $cartPriceRuleId = $request->input('cartPriceRuleId');
        $cartPriceRuleItem = $request->input('cartPriceRuleItem');
        $tagId = Arr::get($cartPriceRuleItem, 'tagId');
        $quantity = (int) Arr::get($cartPriceRuleItem, 'quantity');

        try {
            $this->dispatch(new CreateCartPriceRuleTagItemCommand(
                $cartPriceRuleId,
                $tagId,
                $quantity
            ));

            $this->flashSuccess('Cart price rule tag item has been created.');

        } catch (EntityValidatorException $e) {
            $this->flashError('Unable to create cart price rule tag item!');
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
