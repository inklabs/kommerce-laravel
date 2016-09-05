<?php
namespace App\Http\Controllers\Admin\Promotion\Coupon;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Coupon\ListCouponsQuery;
use inklabs\kommerce\Action\Coupon\Query\ListCouponsRequest;
use inklabs\kommerce\Action\Coupon\Query\ListCouponsResponse;

class ListCouponsController extends Controller
{
    public function index(Request $httpRequest)
    {
        $request = new ListCouponsRequest(
            $httpRequest->query('q'),
            $this->getPaginationDTO(20)
        );

        $response = new ListCouponsResponse();
        $this->dispatchQuery(new ListCouponsQuery($request, $response));

        $coupons = $response->getCouponDTOs();
        $pagination = $response->getPaginationDTO();

        return $this->renderTemplate(
            'admin/coupon/index.twig',
            [
                'coupons' => $coupons,
                'pagination' => $pagination,
            ]
        );
    }
}
