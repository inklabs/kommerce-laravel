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
        $queryString = $httpRequest->query('q');

        $request = new ListCouponsRequest(
            $queryString,
            $this->getPaginationDTO(20)
        );

        $response = new ListCouponsResponse();
        $this->dispatchQuery(new ListCouponsQuery($request, $response));

        $coupons = $response->getCouponDTOs();
        $pagination = $response->getPaginationDTO();

        return $this->renderTemplate(
            '@theme/admin/coupon/index.twig',
            [
                'coupons' => $coupons,
                'pagination' => $pagination,
                'queryString' => $queryString,
            ]
        );
    }
}
