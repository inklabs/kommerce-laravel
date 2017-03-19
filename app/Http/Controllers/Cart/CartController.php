<?php
namespace App\Http\Controllers\Cart;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Cart\AddCartItemCommand;
use inklabs\kommerce\Action\Cart\AddCouponToCartCommand;
use inklabs\kommerce\Action\Cart\DeleteCartItemCommand;
use inklabs\kommerce\Action\Cart\RemoveCouponFromCartCommand;
use inklabs\kommerce\Action\Cart\SetExternalShipmentRateCommand;
use inklabs\kommerce\Action\Cart\UpdateCartItemQuantityCommand;
use inklabs\kommerce\Action\Shipment\GetLowestShipmentRatesByDeliveryMethodQuery;
use inklabs\kommerce\ActionResponse\Shipment\GetLowestShipmentRatesByDeliveryMethodResponse;
use inklabs\kommerce\Exception\EntityValidatorException;
use inklabs\kommerce\EntityDTO\CartDTO;
use inklabs\kommerce\EntityDTO\OrderAddressDTO;
use inklabs\kommerce\EntityDTO\ParcelDTO;
use inklabs\kommerce\EntityDTO\ShipmentRateDTO;
use inklabs\kommerce\Exception\KommerceException;
use inklabs\kommerce\InputDTO\TextOptionValueDTO;

class CartController extends Controller
{
    public function getShow()
    {
        $cart = $this->getCart();

        return $this->renderTemplate(
            '@store/cart/show.twig',
            [
                'cart' => $cart,
                'recommendedProducts' => $this->getRelatedProducts($cart, 4),
            ]
        );
    }

    public function getAdded(Request $request, $cartItemId)
    {
        $cart = $this->getCart();

        if ($cart->totalItems < 1) {
            return redirect('cart');
        }

        $addedCartItem = null;

        $cartProductIds = [];
        foreach ($cart->cartItems as $cartItem) {
            if ($cartItem->id->getHex() === $cartItemId) {
                $addedCartItem = $cartItem;
            }
            $cartProductIds[] = $cartItem->product->id->getHex();
        }

        if ($addedCartItem == null) {
            $this->flashError('This product is not in your cart.');
            return redirect('cart');
        }

        return $this->renderTemplate(
            '@store/cart/added.twig',
            [
                'cart' => $cart,
                'cartItem' => $addedCartItem,
                'recommendedProducts' => $this->getRecommendedProducts($cartProductIds, 12),
            ]
        );
    }

    public function getEstimateShipping()
    {
        // TODO: Update to grab current zip and selected rate

        return $this->renderTemplate(
            '@store/cart/estimate-shipping.twig'
        );
    }

    public function postEstimateShipping(Request $request)
    {
        // TODO: Notify when cart is empty

        $shipping = $request->input('shipping');
        $zip5 = $request->input('shipping.zip5');
        $isResidential = (bool) $request->input('shipping.isResidential');
        $shipmentRates = [];

        if (! empty($zip5)) {
            $cart = $this->getCart();
            $shipmentRates = $this->getTrimmedShipmentRates($request, $cart, $zip5, $isResidential);
        }

        return $this->renderTemplate(
            '@store/cart/estimate-shipping.twig',
            [
                'shipping' => $shipping,
                'shipmentRates' => $shipmentRates,
            ]
        );
    }

    public function postApplyShippingMethod(Request $request)
    {
        $shipping = $request->input('shipping');

        if (empty($shipping)) {
            $this->flashError('Shipping method missing');
            return redirect('cart');
        }

        $cart = $this->getCart();
        $shipmentRateExternalId = $request->input('shipping.shipmentRateExternalId');
        $zip5 = $request->input('shipping.zip5');
        $isResidential = (bool) $request->input('shipping.isResidential');

        $this->applyShipment($cart, $zip5, $isResidential, $shipmentRateExternalId);
        return redirect('cart');
    }

    public function postAddItem(Request $request)
    {
        $productId = $request->input('id');
        $quantity = $request->input('quantity');
        $options = $request->input('option', []);
        $textOptions = $request->input('textOption', []);

        $optionProductIds = [];
        $optionValueIds = [];
        $textOptionValueDTOs = [];

        foreach ($options as $option) {
            list($optionCode, $value) = explode('-', $option);
            if ($optionCode === 'OV') {
                $optionValueIds[] = $value;
            } elseif ($optionCode === 'OP') {
                $optionProductIds[] = $value;
            }
        }

        foreach ($textOptions as $textOptionId => $value) {
            $value = trim($value);

            if ($value === '') {
                continue;
            }
            $textOptionValueDTOs[] = new TextOptionValueDTO($textOptionId, $value);
        }

        try {
            $addCartItemCommand = new AddCartItemCommand(
                $this->getCartId(),
                $productId,
                $quantity,
                $optionProductIds,
                $optionValueIds,
                $textOptionValueDTOs
            );
            $this->dispatch($addCartItemCommand);
            $cartItemId = $addCartItemCommand->getCartItemId();

            $this->flashSuccess($quantity . ' ' . ngettext('item', 'items', $quantity) . ' added to Cart');

            return redirect('cart/added/' . $cartItemId->getHex());
        } catch (KommerceException $e) {
            $this->flashError('Unable to add item');
            return redirect('cart');
        }
    }

    public function postApplyCoupon(Request $request)
    {
        $couponCode = strtoupper($request->input('coupon_code'));

        try {
            $this->dispatch(new AddCouponToCartCommand(
                $this->getCartId(),
                $couponCode
            ));
            $this->flashSuccess('Coupon code added');
        } catch (KommerceException $e) {
            $this->flashError('Unable to add Coupon.');
        }

        return redirect('cart');
    }

    public function postRemoveCoupon(Request $request)
    {
        $couponId = $request->input('couponId');
        $cartId = $this->getCartId();

        try {
            $this->dispatch(new RemoveCouponFromCartCommand(
                $cartId,
                $couponId
            ));
            $this->flashSuccess('Coupon removed');
        } catch (KommerceException $e) {
            $this->flashError('Unable to remove Coupon.');
        }

        return redirect('cart');
    }

    public function postUpdateQuantity(Request $request)
    {
        $cartItemId = $request->input('id');
        $quantity = (int) $request->input('quantity');

        try {
            if ($quantity > 0) {
                $this->dispatch(new UpdateCartItemQuantityCommand(
                    $cartItemId,
                    $quantity
                ));
                $this->flashSuccess('Quantity updated to ' . $quantity);
            } else {
                $this->dispatch(new DeleteCartItemCommand($cartItemId));
                $this->flashSuccess('Removed item from Cart');
            }
        } catch (KommerceException $e) {
            $this->flashError('Unable to modify item in Cart');
        }

        return redirect('cart');
    }

    public function postDeleteItem(Request $request)
    {
        $cartItemId = $request->input('id');

        try {
            $this->dispatch(new DeleteCartItemCommand($cartItemId));
            $this->flashSuccess('Removed item from Cart');
        } catch (KommerceException $e) {
            $this->flashError('Unable to remove item');
        }

        return redirect('cart');
    }

    /**
     * @param Request $parentRequest
     * @param CartDTO $cart
     * @param string $zip5
     * @param bool $isResidential
     * @return ShipmentRateDTO[]
     */
    private function getTrimmedShipmentRates(Request $parentRequest, CartDTO $cart, $zip5, $isResidential)
    {
        $toAddress = new OrderAddressDTO;
        $toAddress->zip5 = $zip5;
        $toAddress->country = 'US';
        $toAddress->isResidential = $isResidential;

        $defaultLength = 9;
        $defaultWidth = 9;
        $defaultHeight = 9;

        $parcel = new ParcelDTO;
        $parcel->length = $defaultLength;
        $parcel->width = $defaultWidth;
        $parcel->height = $defaultHeight;
        $parcel->weight = $cart->shippingWeight;

        /** @var GetLowestShipmentRatesByDeliveryMethodResponse $response */
        $response = $this->dispatchQuery(new GetLowestShipmentRatesByDeliveryMethodQuery($toAddress, $parcel));

        $shipmentRateDTOs = $response->getShipmentRateDTOs();

        if (empty($shipmentRateDTOs)) {
            $this->flashError('Unable to estimate shipping.');
        }

        return $shipmentRateDTOs;
    }

    /**
     * @param CartDTO $cart
     * @param string $zip5
     * @param bool $isResidential
     * @param string $shipmentRateExternalId
     */
    private function applyShipment(CartDTO $cart, $zip5, $isResidential, $shipmentRateExternalId)
    {
        // TODO: Load State from zipcode
        $state = 'CA';
//        $state = DB::select('state')
//            ->from('zipcode')
//            ->where('zipcode', '=', $zip5)
//            ->limit(1)
//            ->execute()
//            ->get('state');

        $shippingAddressDTO = new OrderAddressDTO;
        $shippingAddressDTO->zip5 = $zip5;
        $shippingAddressDTO->state = $state;
        $shippingAddressDTO->isResidential = $isResidential;
        $shippingAddressDTO->country = 'US';

        try {
            $this->dispatch(new SetExternalShipmentRateCommand(
                $cart->id,
                $shipmentRateExternalId,
                $shippingAddressDTO
            ));

            $this->flashSuccess('Added Shipping Method.');
        } catch (EntityValidatorException $e) {
            $this->flashGenericWarning();
        }
    }
}
