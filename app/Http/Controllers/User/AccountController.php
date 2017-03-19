<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Order\GetOrdersByUserQuery;
use inklabs\kommerce\ActionResponse\Order\GetOrdersByUserResponse;

class AccountController extends Controller
{
    public function index()
    {
        $user = $this->getUserFromSessionOrAbort();

        /** @var GetOrdersByUserResponse $response */
        $response = $this->dispatchQuery(new GetOrdersByUserQuery($user->id->getHex()));

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
