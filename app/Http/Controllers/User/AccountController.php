<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Order\GetOrdersByUserQuery;
use inklabs\kommerce\Action\Order\Query\GetOrdersByUserRequest;
use inklabs\kommerce\Action\Order\Query\GetOrdersByUserResponse;

class AccountController extends Controller
{
    public function index()
    {
        $user = $this->getUserFromSessionOrAbort();

        $request = new GetOrdersByUserRequest($user->id->getHex());
        $response = new GetOrdersByUserResponse();
        $this->dispatchQuery(new GetOrdersByUserQuery($request, $response));

        return $this->renderTemplate(
            '@store/user/account.twig',
            [
                'user' => $user,
                'orders' => $response->getOrderDTOsWithAllData(),
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
            '@store/user/view-order.twig',
            [
                'order' => $order,
            ]
        );
    }
}
