<?php
namespace App\Http\Controllers\Admin\Promotion\CartPriceRule;

use App\Http\Controllers\Controller;
use App\Lib\Arr;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\CartPriceRule\UpdateCartPriceRuleCommand;
use inklabs\kommerce\Exception\EntityValidatorException;

class EditCartPriceRuleController extends Controller
{
    public function get($cartPriceRuleId)
    {
        $cartPriceRule = $this->getCartPriceRuleWithAllData($cartPriceRuleId);

        return $this->renderTemplate(
            '@admin/promotion/cart-price-rule/edit.twig',
            [
                'cartPriceRule' => $cartPriceRule,
            ]
        );
    }

    public function post(Request $request)
    {
        $cartPriceRuleId = $request->input('cartPriceRuleId');
        $cartPriceRuleValues = $request->input('cartPriceRule');
        $name = trim(Arr::get($cartPriceRuleValues, 'name'));
        $maxRedemptions = $this->getIntOrNull(Arr::get($cartPriceRuleValues, 'maxRedemptions'));
        $reducesTaxSubtotal = (bool) Arr::get($cartPriceRuleValues, 'reducesTaxSubtotal', false);
        $startAt = $this->getTimestampFromDateTimeTimezoneInput($cartPriceRuleValues['start']);
        $endAt = $this->getTimestampFromDateTimeTimezoneInput($cartPriceRuleValues['end']);

        try {
            $this->dispatch(new UpdateCartPriceRuleCommand(
                $name,
                $maxRedemptions,
                $reducesTaxSubtotal,
                $startAt,
                $endAt,
                $cartPriceRuleId
            ));

            $this->flashSuccess('CartPriceRule has been saved.');
            return redirect()->route(
                'admin.cart-price-rule.edit',
                [
                    'cartPriceRuleId' => $cartPriceRuleId,
                ]
            );
        } catch (EntityValidatorException $e) {
            $this->flashError('Unable to save cartPriceRule!');
            $this->flashFormErrors($e->getErrors());
        }

        return $this->get($cartPriceRuleId);
    }
}
