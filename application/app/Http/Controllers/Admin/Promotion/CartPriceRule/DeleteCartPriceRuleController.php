<?php
namespace App\Http\Controllers\Admin\Promotion\CartPriceRule;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\CartPriceRule\DeleteCartPriceRuleCommand;
use inklabs\kommerce\Exception\KommerceException;

class DeleteCartPriceRuleController extends Controller
{
    public function post(Request $request)
    {
        $cartPriceRuleId = $request->input('cartPriceRuleId');

        try {
            $this->dispatch(new DeleteCartPriceRuleCommand($cartPriceRuleId));
            $this->flashSuccess('Success removing cart price rule');
        } catch (KommerceException $e) {
            $this->flashError('Unable remove cart price rule.');
        }

        return redirect()->route('admin.cart-price-rule');
    }
}
