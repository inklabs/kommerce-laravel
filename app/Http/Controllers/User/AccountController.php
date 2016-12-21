<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\tests\Helper\Entity\DummyData;

class AccountController extends Controller
{
    public function index()
    {
        $dummyData = new DummyData();
        $user = $this->getDTOBuilderFactory()->getUserDTOBuilder($dummyData->getUser())->build();
        $order = $dummyData->getOrderFull();
        $order->setReferenceNumber('xxx-xxx-xxxx');
        $orders[] = $this->getDTOBuilderFactory()->getOrderDTOBuilder($order)->build();

        return $this->renderTemplate(
            '@theme/user/account.twig',
            [
                'user' => $user,
                'orders' => $orders,
            ]
        );
    }

    public function viewOrder(Request $request, $orderId)
    {
        $order = $this->getOrderWithAllData($orderId);

        // TODO: Check order ownership
//        if ($order->user === null || ! $order->user->id->equals($this->user->id)) {
//            abort(403);
//        }

        return $this->renderTemplate(
            '@theme/user/view-order.twig',
            [
                'order' => $order,
            ]
        );
    }
}
