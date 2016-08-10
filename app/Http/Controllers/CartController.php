<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use inklabs\kommerce\Action\Cart\AddCartItemCommand;
use inklabs\kommerce\Action\Cart\AddCouponToCartCommand;
use inklabs\kommerce\Action\Cart\DeleteCartItemCommand;
use inklabs\kommerce\Action\Cart\SetExternalShipmentRateCommand;
use inklabs\kommerce\Action\Cart\UpdateCartItemQuantityCommand;
use inklabs\kommerce\Action\Product\GetRandomProductsQuery;
use inklabs\kommerce\Action\Product\GetRelatedProductsQuery;
use inklabs\kommerce\Action\Product\Query\GetRandomProductsRequest;
use inklabs\kommerce\Action\Product\Query\GetRandomProductsResponse;
use inklabs\kommerce\Action\Product\Query\GetRelatedProductsRequest;
use inklabs\kommerce\Action\Product\Query\GetRelatedProductsResponse;
use inklabs\kommerce\Action\Shipment\GetLowestShipmentRatesByDeliveryMethodQuery;
use inklabs\kommerce\Action\Shipment\Query\GetLowestShipmentRatesByDeliveryMethodRequest;
use inklabs\kommerce\Action\Shipment\Query\GetLowestShipmentRatesByDeliveryMethodResponse;
use inklabs\kommerce\Entity\EntityValidatorException;
use inklabs\kommerce\EntityDTO\CartDTO;
use inklabs\kommerce\EntityDTO\OrderAddressDTO;
use inklabs\kommerce\EntityDTO\ParcelDTO;
use inklabs\kommerce\EntityDTO\ProductDTO;
use inklabs\kommerce\EntityDTO\ShipmentRateDTO;
use inklabs\kommerce\Exception\KommerceException;
use inklabs\kommerce\InputDTO\TextOptionValueDTO;

class CartController extends Controller
{
    public function getIndex()
    {
        $cart = $this->getCart();

        $this->displayTemplate(
            'cart/show.twig',
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
            $this->flashError($request, 'This product is not in your cart.');
            return redirect('cart');
        }

        $this->displayTemplate(
            'cart/added.twig',
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

        $this->displayTemplate(
            'cart/estimate_shipping.twig'
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

        $this->displayTemplate(
            'cart/estimate_shipping.twig',
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
            $this->flashError($request, 'Shipping method missing');
            return redirect('cart');
        }

        $cart = $this->getCart();
        $shipmentRateExternalId = $request->input('shipping.shipmentRateExternalId');
        $zip5 = $request->input('shipping.zip5');
        $isResidential = (bool) $request->input('shipping.isResidential');

        $this->applyShipment($request, $cart, $zip5, $isResidential, $shipmentRateExternalId);
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

            $this->flashSuccess($request, $quantity . ' ' . ngettext('item', 'items', $quantity) . ' added to Cart');

            return redirect('cart/added/' . $cartItemId->getHex());
        } catch (KommerceException $e) {
            $this->flashError($request, 'Unable to add item');
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
        } catch (KommerceException $e) {
            $this->flashError($request, 'Unable to add Coupon.');
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
                $this->flashSuccess($request, 'Quantity updated to ' . $quantity);
            } else {
                $this->dispatch(new DeleteCartItemCommand($cartItemId));
                $this->flashSuccess($request, 'Removed item from Cart');
            }
        } catch (KommerceException $e) {
            $this->flashError($request, 'Unable to modify item in Cart');
        }

        return redirect('cart');
    }

    public function postDeleteItem(Request $request)
    {
        $cartItemId = $request->input('id');

        try {
            $this->dispatch(new DeleteCartItemCommand($cartItemId));
            $this->flashSuccess($request, 'Removed item from Cart');
        } catch (KommerceException $e) {
            $this->flashError($request, 'Unable to remove item');
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

        $request = new GetLowestShipmentRatesByDeliveryMethodRequest($toAddress, $parcel);
        $response = new GetLowestShipmentRatesByDeliveryMethodResponse();
        $this->dispatchQuery(new GetLowestShipmentRatesByDeliveryMethodQuery($request, $response));

        $shipmentRateDTOs = $response->getShipmentRateDTOs();

        if (empty($shipmentRateDTOs)) {
            $this->flashError($parentRequest, 'Unable to estimate shipping.');
        }

        return $shipmentRateDTOs;
    }

    /**
     * @param Request $request
     * @param CartDTO $cart
     * @param string $zip5
     * @param bool $isResidential
     * @param string $shipmentRateExternalId
     */
    private function applyShipment(Request $request, CartDTO $cart, $zip5, $isResidential, $shipmentRateExternalId)
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

            $this->flashSuccess($request, 'Added Shipping Method.');
        } catch (EntityValidatorException $e) {
            $this->flashGenericWarning($request);
        }
    }

    protected function getRelatedProducts(CartDTO $cartDTO, $limit = 4)
    {
        $cartProductIds = [];
        foreach ($cartDTO->cartItems as $cartItem) {
            $cartProductIds[] = $cartItem->product->id->getHex();
        }

        return $this->getRecommendedProducts($cartProductIds, $limit);
    }

    /**
     * @param string[] $productIds
     * @param int $limit
     * @return ProductDTO[]
     */
    protected function getRecommendedProducts($productIds, $limit)
    {
        // Hot-wire random for now, until tags get into the db
        return $this->getRandomProducts($limit);

        $request = new GetRelatedProductsRequest($productIds, $limit);
        $response = new GetRelatedProductsResponse($this->getPricing());
        $this->dispatchQuery(new GetRelatedProductsQuery($request, $response));

        return $response->getProductDTOs();
    }

    protected function getRandomProducts($limit)
    {
        $request = new GetRandomProductsRequest($limit);
        $response = new GetRandomProductsResponse($this->getPricing());
        $this->dispatchQuery(new GetRandomProductsQuery($request, $response));

        return $response->getProductDTOs();
    }
}
