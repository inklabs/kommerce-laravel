<?php
namespace App\Http\Controllers\Admin\Promotion\Coupon;

use App\Http\Controllers\Controller;
use App\Lib\Arr;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Coupon\CreateCouponCommand;
use inklabs\kommerce\Action\Coupon\UpdateCouponCommand;
use inklabs\kommerce\Entity\PromotionType;
use inklabs\kommerce\EntityDTO\CouponDTO;
use inklabs\kommerce\Exception\EntityValidatorException;

class EditCouponController extends Controller
{
    public function getNew()
    {
        return $this->renderTemplate(
            '@theme/admin/coupon/new.twig',
            [
                'promotionTypes' => PromotionType::getNameMap(),
            ]
        );
    }

    public function postNew(Request $request)
    {
        $coupon = new CouponDTO();
        $this->updateCouponDTOFromPost($coupon, $request->input('coupon'));

        try {
            $command = new CreateCouponCommand(
                $coupon->code,
                $coupon->flagFreeShipping,
                $coupon->minOrderValue,
                $coupon->maxOrderValue,
                $coupon->canCombineWithOtherCoupons,
                $coupon->name,
                $coupon->type->id,
                $coupon->value,
                $coupon->reducesTaxSubtotal,
                $coupon->maxRedemptions,
                $coupon->start,
                $coupon->end
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
                'coupon' => $coupon,
                'promotionTypes' => PromotionType::getNameMap(),
            ]
        );
    }

    public function getEdit($couponId)
    {
        $coupon = $this->getCoupon($couponId);

        return $this->renderTemplate(
            '@theme/admin/coupon/edit.twig',
            [
                'coupon' => $coupon,
                'promotionTypes' => PromotionType::getNameMap(),
            ]
        );
    }

    public function postEdit(Request $request)
    {
        $couponId = $request->input('couponId');
        $coupon = $this->getCoupon($couponId);

        $this->updateCouponDTOFromPost($coupon, $request->input('coupon'));

        try {
            $this->dispatch(new UpdateCouponCommand($coupon));

            $this->flashSuccess('Coupon has been saved.');
            return redirect()->route(
                'admin.coupon.edit',
                [
                    'couponId' => $couponId,
                ]
            );
        } catch (EntityValidatorException $e) {
            $this->flashError('Unable to save coupon!');
            $this->flashFormErrors($e->getErrors());
        }

        return $this->renderTemplate(
            '@theme/admin/coupon/edit.twig',
            [
                'coupon' => $coupon,
                'promotionTypes' => PromotionType::getNameMap(),
            ]
        );
    }

    private function updateCouponDTOFromPost(CouponDTO & $couponDTO, array $couponValues)
    {
        $typeId = (int) Arr::get($couponValues, 'type');
        $promotionType = PromotionType::createById($typeId);
        $promotionTypeDTO = $this->getDTOBuilderFactory()
            ->getPromotionTypeDTOBuilder($promotionType)
            ->build();

        if ($promotionTypeDTO->isFixed || $promotionTypeDTO->isExact) {
            $value = (int) (Arr::get($couponValues, 'value') * 100);
        } else {
            $value = (int) Arr::get($couponValues, 'value');
        }

        $couponDTO->name = trim(Arr::get($couponValues, 'name'));
        $couponDTO->code = trim(Arr::get($couponValues, 'code'));
        $couponDTO->type = $promotionTypeDTO;
        $couponDTO->value = $value;
        $couponDTO->minOrderValue = $this->getCentsOrNull(Arr::get($couponValues, 'minOrderValue'));
        $couponDTO->maxOrderValue = $this->getCentsOrNull(Arr::get($couponValues, 'maxOrderValue'));
        $couponDTO->maxRedemptions = (int) Arr::get($couponValues, 'maxRedemptions');
        $couponDTO->flagFreeShipping = Arr::get($couponValues, 'flagFreeShipping', false);
        $couponDTO->reducesTaxSubtotal = Arr::get($couponValues, 'reducesTaxSubtotal', false);
        $couponDTO->canCombineWithOtherCoupons = Arr::get($couponValues, 'canCombineWithOtherCoupons', false);

        // TODO:
        //$couponDTO->start =
        //$couponDTO->end =
    }
}
