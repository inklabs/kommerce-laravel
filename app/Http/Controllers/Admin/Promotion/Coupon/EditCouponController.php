<?php
namespace App\Http\Controllers\Admin\Promotion\Coupon;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EditCouponController extends Controller
{
    public function get($couponId)
    {
        $coupon = $this->getCoupon($couponId);

        return $this->renderTemplate(
            'admin/coupon/edit.twig',
            [
                'coupon' => $coupon,
            ]
        );
    }
}
