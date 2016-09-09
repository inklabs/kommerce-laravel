<?php
namespace App\Http\Controllers\Admin\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Order\GetOrderQuery;
use inklabs\kommerce\Action\Order\Query\GetOrderRequest;
use inklabs\kommerce\Action\Order\Query\GetOrderResponse;
use inklabs\kommerce\Exception\EntityNotFoundException;

class AddShipmentController extends Controller
{
    public function get($orderId)
    {
        try {
            $request = new GetOrderRequest($orderId);
            $response = new GetOrderResponse();
            $this->dispatchQuery(new GetOrderQuery($request, $response));
        } catch (EntityNotFoundException $e) {
            return abort(404);
        }

        return $this->renderTemplate(
            'admin/order/add-shipment.twig',
            [
                'order' => $response->getOrderDTOWithAllData(),
            ]
        );
    }

    public function post(Request $request)
    {
        echo 'TODO:';
        dd($request->input());
    }
}
