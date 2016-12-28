<?php
namespace App\Http\Controllers\Admin\Promotion\Coupon;

use App\Http\Controllers\Controller;
use App\Lib\Arr;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Coupon\CreateCouponCommand;
use inklabs\kommerce\Entity\PromotionType;
use inklabs\kommerce\Exception\EntityValidatorException;

class CreateCouponController extends Controller
{
    public function get()
    {
        return $this->renderTemplate(
            '@theme/admin/coupon/new.twig',
            [
                'promotionTypes' => PromotionType::getSlugNameMap(),
            ]
        );
    }

    public function post(Request $request)
    {
        $couponValues = $request->input('coupon');

        $promotionTypeSlug = Arr::get($couponValues, 'type');
        $value = (int) Arr::get($couponValues, 'value');

        if ($promotionTypeSlug === 'fixed' || $promotionTypeSlug === 'exact') {
            $value = $value * 100;
        }

        $name = trim(Arr::get($couponValues, 'name'));
        $code = trim(Arr::get($couponValues, 'code'));
        $minOrderValue = $this->getCentsOrNull(Arr::get($couponValues, 'minOrderValue'));
        $maxOrderValue = $this->getCentsOrNull(Arr::get($couponValues, 'maxOrderValue'));
        $maxRedemptions = (int) Arr::get($couponValues, 'maxRedemptions');
        $flagFreeShipping = (bool) Arr::get($couponValues, 'flagFreeShipping', false);
        $reducesTaxSubtotal = (bool) Arr::get($couponValues, 'reducesTaxSubtotal', false);
        $canCombineWithOtherCoupons = (bool) Arr::get($couponValues, 'canCombineWithOtherCoupons', false);

        $startAt = $this->getTimestampFromDateTimeTimezoneInput($couponValues['start']);
        $endAt = $this->getTimestampFromDateTimeTimezoneInput($couponValues['end']);

        try {
            $command = new CreateCouponCommand(
                $code,
                $flagFreeShipping,
                $minOrderValue,
                $maxOrderValue,
                $canCombineWithOtherCoupons,
                $name,
                $promotionTypeSlug,
                $value,
                $reducesTaxSubtotal,
                $maxRedemptions,
                $startAt,
                $endAt
            );

            $this->dispatch($command);

            $this->flashSuccess('Coupon has been created.');
            return redirect()->route(
                'admin.coupon.edit',
                [
                    'couponId' => $command->getCouponId()->gethex(),
                ]
            );
        } catch (EntityValidatorException $e) {
            $this->flashError('Unable to create coupon!');
            $this->flashFormErrors($e->getErrors());
        }

        return $this->renderTemplate(
            '@theme/admin/coupon/new.twig',
            [
                'promotionTypes' => PromotionType::getSlugNameMap(),
            ]
        );
    }
}
