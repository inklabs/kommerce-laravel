<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use inklabs\kommerce\Action\Cart\AddCartItemCommand;
use inklabs\kommerce\Action\Cart\AddCouponToCartCommand;
use inklabs\kommerce\Action\Cart\DeleteCartItemCommand;
use inklabs\kommerce\Action\Cart\UpdateCartItemQuantityCommand;
use inklabs\kommerce\Action\Product\GetRandomProductsQuery;
use inklabs\kommerce\Action\Product\GetRelatedProductsQuery;
use inklabs\kommerce\Action\Product\Query\GetRandomProductsRequest;
use inklabs\kommerce\Action\Product\Query\GetRandomProductsResponse;
use inklabs\kommerce\Action\Product\Query\GetRelatedProductsRequest;
use inklabs\kommerce\Action\Product\Query\GetRelatedProductsResponse;
use inklabs\kommerce\EntityDTO\CartDTO;
use inklabs\kommerce\EntityDTO\ProductDTO;
use inklabs\kommerce\Exception\KommerceException;

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

    public function postAddItem(Request $request)
    {
        $productId = $request->input('id');
        $quantity = $request->input('quantity');
        $options = $request->input('options');
        $textOptions = $request->input('textOption');

        $optionProductIds = [];
        $optionValueIds = [];
        $textOptionValues = [];

//
//        foreach ($options as $option) {
//            list($optionCode, $value) = explode('-', $option);
//            if ($optionCode === 'OV') {
//                $optionValueIds[] = $value;
//            } elseif ($optionCode === 'OP') {
//                $optionProductIds[] = $value;
//            }
//        }
//
//        foreach ($textOptions as $textOptionId => $value) {
//            $value = trim($value);
//
//            if ($value === '') {
//                continue;
//            }
//            $textOptionValues[$textOptionId] = $value;
//        }

        try {
            $addCartCommand = new AddCartItemCommand(
                $this->getCartId(),
                $productId,
                $quantity,
                $optionProductIds,
                $optionValueIds,
                $textOptionValues
            );
            $this->dispatch($addCartCommand);
            $cartItemId = $addCartCommand->getCartItemId();

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
