<?php
namespace App\Http\Controllers\Admin\Promotion\CartPriceRule;

use App\Http\Controllers\Controller;
use App\Lib\Arr;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\CartPriceRule\CreateCartPriceRuleCommand;
use inklabs\kommerce\Action\CartPriceRule\UpdateCartPriceRuleCommand;
use inklabs\kommerce\Entity\PromotionType;
use inklabs\kommerce\EntityDTO\CartPriceRuleDTO;
use inklabs\kommerce\Exception\EntityValidatorException;

class EditCartPriceRuleController extends Controller
{
    public function getNew()
    {
        return $this->renderTemplate('@theme/admin/promotion/cart-price-rule/new.twig');
    }

    public function postNew(Request $request)
    {
        $cartPriceRule = new CartPriceRuleDTO();
        $this->updateCartPriceRuleDTOFromPost($cartPriceRule, $request->input('cartPriceRule'));

        try {
            $command = new CreateCartPriceRuleCommand(
                $cartPriceRule->name,
                $cartPriceRule->reducesTaxSubtotal,
                $cartPriceRule->maxRedemptions,
                $cartPriceRule->start,
                $cartPriceRule->end
            );
            $this->dispatch($command);

            $this->flashSuccess('CartPriceRule has been created.');
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

        return $this->renderTemplate(
            '@theme/admin/promotion/cart-price-rule/new.twig',
            [
                'cartPriceRule' => $cartPriceRule,
                'promotionTypes' => PromotionType::getNameMap(),
            ]
        );
    }

    public function getEdit($cartPriceRuleId)
    {
        $cartPriceRule = $this->getCartPriceRule($cartPriceRuleId);

        return $this->renderTemplate(
            '@theme/admin/promotion/cart-price-rule/edit.twig',
            [
                'cartPriceRule' => $cartPriceRule,
                'promotionTypes' => PromotionType::getNameMap(),
            ]
        );
    }

    public function postEdit(Request $request)
    {
        $cartPriceRuleId = $request->input('cartPriceRuleId');
        $cartPriceRule = $this->getCartPriceRule($cartPriceRuleId);

        $this->updateCartPriceRuleDTOFromPost($cartPriceRule, $request->input('cartPriceRule'));

        try {
            $this->dispatch(new UpdateCartPriceRuleCommand($cartPriceRule));

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

        return $this->renderTemplate(
            '@theme/admin/promotion/cart-price-rule/edit.twig',
            [
                'cartPriceRule' => $cartPriceRule,
                'promotionTypes' => PromotionType::getNameMap(),
            ]
        );
    }

    private function updateCartPriceRuleDTOFromPost(CartPriceRuleDTO & $cartPriceRuleDTO, array $cartPriceRuleValues)
    {
        $cartPriceRuleDTO->name = trim(Arr::get($cartPriceRuleValues, 'name'));
        $cartPriceRuleDTO->maxRedemptions = $this->getIntOrNull(Arr::get($cartPriceRuleValues, 'maxRedemptions'));
        $cartPriceRuleDTO->reducesTaxSubtotal = (bool) Arr::get($cartPriceRuleValues, 'reducesTaxSubtotal', false);

        // TODO:
        //$cartPriceRuleDTO->start =
        //$cartPriceRuleDTO->end =
    }
}
