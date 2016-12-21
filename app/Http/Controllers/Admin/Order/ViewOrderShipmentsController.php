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
        $order = $this->getOrderWithAllData($orderId);

        return $this->renderTemplate(
            '@theme/admin/order/shipments.twig',
            [
                'order' => $order,
            ]
        );
    }
}
