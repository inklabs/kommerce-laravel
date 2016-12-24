<?php
namespace App\Http\Controllers\Admin\Promotion\Coupon;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Coupon\DeleteCouponCommand;
use inklabs\kommerce\Exception\KommerceException;

class DeleteCouponController extends Controller
{
    public function post(Request $request)
    {
        $couponId = $request->input('couponId');

        try {
            $this->dispatch(new DeleteCouponCommand($couponId));
            $this->flashSuccess('Success removing coupon');
        } catch (KommerceException $e) {
            $this->flashError('Unable remove coupon.');
        }

        return redirect()->route('admin.coupon');
    }
}
