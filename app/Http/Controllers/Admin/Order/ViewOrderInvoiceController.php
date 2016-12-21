<?php
namespace App\Http\Controllers\Admin\Order;

use App\Http\Controllers\Controller;

class ViewOrderInvoiceController extends Controller
{
    public function index($orderId)
    {
        $order = $this->getOrderWithAllData($orderId);

        return $this->renderTemplate(
            '@theme/admin/order/invoice.twig',
            [
                'order' => $order,
            ]
        );
    }
}
