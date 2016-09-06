<?php
namespace App\Http\Controllers\Admin\Order;

use App\Http\Controllers\Controller;
use inklabs\kommerce\Action\Order\GetOrderQuery;
use inklabs\kommerce\Action\Order\Query\GetOrderRequest;
use inklabs\kommerce\Action\Order\Query\GetOrderResponse;
use inklabs\kommerce\Exception\EntityNotFoundException;

class ViewOrderShipmentsController extends Controller
{
    public function index($orderId)
    {
        try {
            $request = new GetOrderRequest($orderId);
            $response = new GetOrderResponse();
            $this->dispatchQuery(new GetOrderQuery($request, $response));
        } catch (EntityNotFoundException $e) {
            return abort(404);
        }

        return $this->renderTemplate(
            'admin/order/shipments.twig',
            [
                'order' => $response->getOrderDTOWithAllData(),
            ]
        );
    }
}
