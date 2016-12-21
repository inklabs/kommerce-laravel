<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use inklabs\kommerce\Action\Order\CreateOrderFromCartCommand;
use inklabs\kommerce\Action\Product\GetRandomProductsQuery;
use inklabs\kommerce\Action\Product\Query\GetRandomProductsRequest;
use inklabs\kommerce\Action\Product\Query\GetRandomProductsResponse;
use inklabs\kommerce\Action\User\CreateUserCommand;
use inklabs\kommerce\Action\User\GetUserByEmailQuery;
use inklabs\kommerce\Action\User\GetUserQuery;
use inklabs\kommerce\Action\User\Query\GetUserByEmailRequest;
use inklabs\kommerce\Action\User\Query\GetUserByEmailResponse;
use inklabs\kommerce\Action\User\Query\GetUserRequest;
use inklabs\kommerce\Action\User\Query\GetUserResponse;
use inklabs\kommerce\Exception\EntityValidatorException;
use inklabs\kommerce\EntityDTO\CreditCardDTO;
use inklabs\kommerce\EntityDTO\OrderAddressDTO;
use inklabs\kommerce\EntityDTO\OrderDTO;
use inklabs\kommerce\EntityDTO\ProductDTO;
use inklabs\kommerce\EntityDTO\UserDTO;
use inklabs\kommerce\Exception\EntityNotFoundException;
use inklabs\kommerce\Exception\InsufficientInventoryException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CheckoutController extends Controller
{
    public function getPay()
    {
        $cart = $this->getCart();

        return $this->renderTemplate(
            '@theme/checkout/pay.twig',
            [
                'cart' => $cart,
                'months' => $this->getMonths(),
                'years' => $this->getYears(),
            ]
        );
    }

    public function getComplete($orderId)
    {
        try {
            $order = $this->getOrderWithAllData($orderId);
        } catch (NotFoundHttpException $e) {
            $this->flashError('Order not found.');
            return redirect('/');
        }

        $recommendedProducts = $this->getRecommendedProductsFromOrder($order);

        return $this->renderTemplate(
            '@theme/checkout/complete.twig',
            [
                'order' => $order,
                'recommendedProducts' => $recommendedProducts,
            ]
        );
    }

    public function postPay(Request $request)
    {
        $cart = $this->getCart();

        if ($cart->cartTotal->shipping === null) {
            $this->flashError('A shipping method must be chosen');
            return redirect('checkout/pay');
        }

        $inputCreditCard = $request->input('creditCard');
        $inputShipping = $request->input('shipping');

        $creditCard = $this->getCreditCardDTOFromArray($inputCreditCard);
        $shippingAddress = $this->getOrderAddressDTOFromArray($inputShipping);
        $billingAddress = clone $shippingAddress;

        try {
            $user = $this->getOrCreateUserFromOrderAddress($billingAddress);

            $createOrderCommand = new CreateOrderFromCartCommand(
                $cart->id->getHex(),
                $user->id->getHex(),
                $this->getRemoteIP4(),
                $creditCard,
                $shippingAddress,
                $billingAddress
            );
            $this->dispatch($createOrderCommand);

            $orderId = $createOrderCommand->getOrderId();

            if ($cart->cartTotal->total > 0) {
                $this->flashTemplateSuccess(
                    '@theme/flash/order-placed.twig',
                    [
                        'cartTotal' => $cart->cartTotal,
                    ]
                );
            } else {
                $this->flashTemplateSuccess(
                    '@theme/flash/order-placed-free.twig',
                    [
                        'cartTotal' => $cart->cartTotal,
                    ]
                );
            }

            return redirect('checkout/complete/' . $orderId->getHex());
        } catch (EntityValidatorException $e) {
            $this->flashError('Unable to create order!');
            $this->flashFormErrors($e->getErrors());
        } catch (InsufficientInventoryException $e) {
            $this->flashError(
                'We are sorry, we do not have sufficient inventory to complete your order.' .
                ' Please <a href="/page/contact">contact us</a> if you would like help with your order.'
            );
        } catch (\Stripe\Error\Base $e) {
            $this->flashError($e->getMessage());
        }

        // TODO: Clean up payments
        return $this->renderTemplate(
            '@theme/checkout/pay.twig',
            [
                'cart' => $cart,
                'shipping' => $inputShipping,
                'creditCard' => $inputCreditCard,
                'months' => $this->getMonths(),
                'years' => $this->getYears(),
            ]
        );
    }

    private function getMonths()
    {
        $months = array('' => 'Month');

        for ($i = 1; $i <= 12; $i++) {
            $num = str_pad($i, 2, '0', STR_PAD_LEFT);
            $months[$num] = $num;
        }

        return $months;
    }

    private function getYears()
    {
        $years = array('' => 'Year');
        $current_year = (int) date('Y');

        for ($i = $current_year; $i <= ($current_year + 12); $i++) {
            $years[$i] = $i;
        }

        return $years;
    }

    private function getOrderAddressDTOFromArray(array $orderAddress)
    {
        $orderAddressDTO = new OrderAddressDTO();
        $orderAddressDTO->firstName = array_get($orderAddress, 'firstName');
        $orderAddressDTO->lastName = array_get($orderAddress, 'lastName');
        $orderAddressDTO->company = array_get($orderAddress, 'company');
        $orderAddressDTO->address1 = array_get($orderAddress, 'address1');
        $orderAddressDTO->address2 = array_get($orderAddress, 'address2');
        $orderAddressDTO->city = array_get($orderAddress, 'city');
        $orderAddressDTO->state = array_get($orderAddress, 'state');
        $orderAddressDTO->zip5 = array_get($orderAddress, 'zip5');
        $orderAddressDTO->zip4 = null;
        $orderAddressDTO->phone = array_get($orderAddress, 'phone');
        $orderAddressDTO->email = array_get($orderAddress, 'email');
        $orderAddressDTO->country = array_get($orderAddress, 'country');
        $orderAddressDTO->isResidential = array_get($orderAddress, 'isResidential');

        return $orderAddressDTO;
    }

    private function getCreditCardDTOFromArray(array $creditCard)
    {
        $creditCardDTO = new CreditCardDTO();
        $creditCardDTO->name = array_get($creditCard, 'name');
        $creditCardDTO->zip5 = array_get($creditCard, 'zip5');
        $creditCardDTO->number = array_get($creditCard, 'number');
        $creditCardDTO->cvc = array_get($creditCard, 'cvc');
        $creditCardDTO->expirationMonth = array_get($creditCard, 'expirationMonth');
        $creditCardDTO->expirationYear = array_get($creditCard, 'expirationYear');

        return $creditCardDTO;
    }

    /**
     * @param OrderAddressDTO $orderAddress
     * @return UserDTO
     */
    protected function getOrCreateUserFromOrderAddress(OrderAddressDTO $orderAddress)
    {
        try {
            $request = new GetUserByEmailRequest($orderAddress->email);
            $response = new GetUserByEmailResponse();
            $this->dispatchQuery(new GetUserByEmailQuery($request, $response));

            return $response->getUserDTO();
        } catch (EntityNotFoundException $e) {
            $user = new UserDTO();
            $user->firstName = $orderAddress->firstName;
            $user->lastName = $orderAddress->lastName;
            $user->email = $orderAddress->email;

            $createUserCommand = new CreateUserCommand($user);
            $this->dispatch($createUserCommand);

            $userId = $createUserCommand->getUserId();

            $request = new GetUserRequest($userId);
            $response = new GetUserResponse();
            $this->dispatchQuery(new GetUserQuery($request, $response));

            return $response->getUserDTO();
        }
    }

    /**
     * @param OrderDTO $order
     * @return ProductDTO[]
     */
    private function getRecommendedProductsFromOrder(OrderDTO $order)
    {
        $productIds = [];

        foreach ($order->orderItems as $orderItem) {
            $productIds[] = $orderItem->product->id->getHex();
        }

        $request = new GetRandomProductsRequest(4);
        $response = new GetRandomProductsResponse($this->getPricing());
        $this->dispatchQuery(new GetRandomProductsQuery($request, $response));

        // TODO: Switch to related products
//        $request = new GetRelatedProductsRequest($productIds, 4);
//        $response = new GetRelatedProductsResponse($this->getPricing());
//        $this->dispatchQuery(new GetRelatedProductsQuery($request, $response));

        return $response->getProductDTOs();
    }
}
