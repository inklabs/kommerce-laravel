<?php
namespace App\Http\Controllers\Admin\Promotion\CartPriceRule;

use App\Http\Controllers\Controller;
use App\Lib\Arr;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\CartPriceRule\CreateCartPriceRuleCommand;
use inklabs\kommerce\Exception\EntityValidatorException;

class CreateCartPriceRuleController extends Controller
{
    public function get()
    {
        return $this->renderTemplate('@theme/admin/promotion/cart-price-rule/new.twig');
    }

    public function post(Request $request)
    {
        $cartPriceRuleValues = $request->input('cartPriceRule');
        $name = trim(Arr::get($cartPriceRuleValues, 'name'));
        $maxRedemptions = $this->getIntOrNull(Arr::get($cartPriceRuleValues, 'maxRedemptions'));
        $reducesTaxSubtotal = (bool) Arr::get($cartPriceRuleValues, 'reducesTaxSubtotal', false);
        $startAt = $this->getTimestampFromDateTimeTimezoneInput($cartPriceRuleValues['start']);
        $endAt = $this->getTimestampFromDateTimeTimezoneInput($cartPriceRuleValues['end']);

        try {
            $command = new CreateCartPriceRuleCommand(
                $name,
                $maxRedemptions,
                $reducesTaxSubtotal,
                $startAt,
                $endAt
            );
            $this->dispatch($command);

            $this->flashSuccess('Cart Price Rule has been created.');
            return redirect()->route(
                'admin.cart-price-rule.edit',
                [
                    'cartPriceRuleId' => $command->getCartPriceRuleId()->getHex(),
                ]
            );
        } catch (EntityValidatorException $e) {
            $this->flashError('Unable to create cartPriceRule!');
            $this->flashFormErrors($e->getErrors());
        }

        return $this->get();
    }
}
