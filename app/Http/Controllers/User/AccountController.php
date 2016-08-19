<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Order\GetOrderQuery;
use inklabs\kommerce\Action\Order\Query\GetOrderRequest;
use inklabs\kommerce\Action\Order\Query\GetOrderResponse;
use inklabs\kommerce\Exception\EntityNotFoundException;
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

        $this->displayTemplate(
            'user/account.twig',
            [
                'user' => $user,
                'orders' => $orders,
            ]
        );
    }

    /**
     * @param Request $request
     * @param string $orderId
     */
    public function viewOrder(Request $request, $orderId)
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
