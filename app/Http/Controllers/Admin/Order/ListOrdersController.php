<?php
namespace App\Http\Controllers\Admin\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ListOrdersController extends Controller
{
    public function index(Request $httpRequest)
    {
        $orders = [];
        $pagination = null;

        return $this->renderTemplate(
            'admin/order/index.twig',
            [
                'orders' => $orders,
                'pagination' => $pagination,
            ]
        );
    }
}
