<?php
namespace App\Http\Controllers\Admin\Order;

use App\Http\Controllers\Controller;

class ViewOrderShipmentsController extends Controller
{
    public function index($orderId)
    {
        $order = $this->getOrderWithAllData($orderId);

        return $this->renderTemplate(
            '@admin/order/shipments.twig',
            [
                'order' => $order,
            ]
        );
    }
}
