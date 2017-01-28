<?php
namespace App\Http\Controllers\Admin\Order;

use App\Http\Controllers\Controller;

class ViewOrderController extends Controller
{
    public function index($orderId)
    {
        $order = $this->getOrderWithAllData($orderId);

        return $this->renderTemplate(
            '@admin/order/view.twig',
            [
                'order' => $order,
            ]
        );
    }
}
