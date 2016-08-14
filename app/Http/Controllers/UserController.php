<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use inklabs\kommerce\Action\Order\GetOrderQuery;
use inklabs\kommerce\Action\Order\Query\GetOrderRequest;
use inklabs\kommerce\Action\Order\Query\GetOrderResponse;
use inklabs\kommerce\Exception\EntityNotFoundException;

class UserController extends Controller
{
    /**
     * @param Request $request
     * @param string $orderId
     */
    public function getViewOrder(Request $request, $orderId)
    {
        try {
            $request = new GetOrderRequest($orderId);
            $response = new GetOrderResponse();
            $this->dispatchQuery(new GetOrderQuery($request, $response));
        } catch (EntityNotFoundException $e) {
            return abort(404);
        }

        $order = $response->getOrderDTOWithAllData();

        // TODO: Check order ownership
//        if ($order->user === null || ! $order->user->id->equals($this->user->id)) {
//            abort(403);
//        }

        $this->displayTemplate(
            'user/view_order.twig',
            [
                'order' => $order,
            ]
        );
    }
}
