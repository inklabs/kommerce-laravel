<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use inklabs\kommerce\Action\Order\GetOrderQuery;
use inklabs\kommerce\Action\Order\Query\GetOrderRequest;
use inklabs\kommerce\Action\Order\Query\GetOrderResponse;
use inklabs\kommerce\Action\User\ChangePasswordCommand;
use inklabs\kommerce\Exception\EntityNotFoundException;
use inklabs\kommerce\Exception\KommerceException;
use inklabs\kommerce\Exception\UserPasswordValidationException;
use inklabs\kommerce\tests\Helper\Entity\DummyData;

class UserController extends Controller
{
    public function getAccount()
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

    public function getChangePassword()
    {
        $dummyData = new DummyData();
        $user = $this->getDTOBuilderFactory()->getUserDTOBuilder($dummyData->getUser())->build();

        $this->displayTemplate(
            'user/change_password.twig'
        );
    }

    public function postChangePassword(Request $request)
    {
        $passwordNew = $request->input('passwordNew');
        $passwordCheck = $request->input('passwordCheck');

        if ($passwordNew !== $passwordCheck) {
            $this->flashError('Please check that your passwords match and try again.');
            return redirect('/user/change-password');
        }

        $userId = 'xxx';

        try {
            $this->dispatch(new ChangePasswordCommand(
                $userId,
                $passwordNew
            ));
        } catch (UserPasswordValidationException $e) {
            $this->flashError($e->getMessage());
            return redirect('/user/change-password');
        } catch (KommerceException $e) {
            $this->flashGenericWarning();
            return redirect('/user/account');
        }

        // TODO: Get correct logged-in user and redirect after successful password change
        dd([$passwordNew, $passwordCheck]);
    }

    /**
     * @param Request $request
     * @param string $orderId
     */
    public function getViewOrder(Request $request, $orderId)
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
