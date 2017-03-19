<?php
namespace App\Http\Controllers\Admin\Promotion\Coupon;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Coupon\ListCouponsQuery;
use inklabs\kommerce\ActionResponse\Coupon\ListCouponsResponse;

class ListCouponsController extends Controller
{
    public function index(Request $httpRequest)
    {
        $queryString = $httpRequest->query('q');

        /** @var ListCouponsResponse $response */
        $response = $this->dispatchQuery(new ListCouponsQuery(
            $queryString,
            $this->getPaginationDTO(20)
        ));

        $coupons = $response->getCouponDTOs();
        $pagination = $response->getPaginationDTO();

        return $this->renderTemplate(
            '@admin/coupon/index.twig',
            [
                'coupons' => $coupons,
                'pagination' => $pagination,
                'queryString' => $queryString,
            ]
        );
    }
}
