<?php
namespace App\Http\Controllers\Admin\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Order\GetOrderQuery;
use inklabs\kommerce\Action\Order\Query\GetOrderRequest;
use inklabs\kommerce\Action\Order\Query\GetOrderResponse;
use inklabs\kommerce\Action\Shipment\AddShipmentTrackingCodeCommand;
use inklabs\kommerce\Entity\ShipmentCarrierType;
use inklabs\kommerce\EntityDTO\OrderItemQtyDTO;
use inklabs\kommerce\Exception\EntityNotFoundException;
use inklabs\kommerce\Exception\EntityValidatorException;
use inklabs\kommerce\Lib\Uuid;

class AddShipmentController extends Controller
{
    public function get($orderId)
    {
        try {
            $request = new GetOrderRequest($orderId);
            $response = new GetOrderResponse();
            $this->dispatchQuery(new GetOrderQuery($request, $response));
        } catch (EntityNotFoundException $e) {
            return abort(404);
        }

        return $this->renderTemplate(
            'admin/order/add-shipment.twig',
            [
                'order' => $response->getOrderDTOWithAllData(),
            ]
        );
    }

    public function post(Request $request)
    {
        $action = $request->input('action');

        if ($action === 'create-with-tracking-code') {
            return $this->createWithTrackingCode($request);
        } elseif ($action === 'create-shipping-label') {
            return $this->createShippingLabel($request);
        }
    }

    public function postAddShipmentWithTrackingCode(Request $httpRequest)
    {
        $orderId = $httpRequest->input('orderId');
        $orderItemQty = $httpRequest->input('orderItemQty');
        $comment = $httpRequest->input('comment');
        $carrier = (int) $httpRequest->input('shipment.carrier');
        $trackingCode = $httpRequest->input('shipment.trackingCode');

        $orderItemQtyDTO = $this->getOrderItemQtyDTO($orderItemQty);

        try {
            $this->dispatch(new AddShipmentTrackingCodeCommand(
                $orderId,
                $orderItemQtyDTO,
                $comment,
                $carrier,
                $trackingCode
            ));

            $this->flashSuccess('Added Tracking Code.');
        } catch (EntityValidatorException $e) {
            $this->flashError('Validation Error');
            //$this->flashError($e->getMessage());
        }

        return redirect()->route('admin.order.shipments', ['orderId' => $orderId]);
    }

    private function createWithTrackingCode(Request $request)
    {
        $orderId = $request->input('orderId');
        $orderItemQty = $request->input('orderItemQty');

        try {
            $request = new GetOrderRequest($orderId);
            $response = new GetOrderResponse();
            $this->dispatchQuery(new GetOrderQuery($request, $response));
        } catch (EntityNotFoundException $e) {
            return abort(404);
        }

        return $this->renderTemplate(
            'admin/order/add-shipment-tracking.twig',
            [
                'order' => $response->getOrderDTOWithAllData(),
                'orderItemQty' => $orderItemQty,
                'shipmentCarrierTypes' => ShipmentCarrierType::getNameMap(),
            ]
        );
    }

    private function createShippingLabel(Request $request)
    {
        echo 'TODO:';
        dd($request->input());
    }

        /**
     * @param $orderItemQty
     * @return OrderItemQtyDTO
     */
    private function getOrderItemQtyDTO($orderItemQty)
    {
        $orderItemQtyDTO = new OrderItemQtyDTO();

        foreach ($orderItemQty as $orderItemId => $qty) {
            $orderItemQtyDTO->addOrderItemQty(Uuid::fromString($orderItemId), $qty);
        }
        return $orderItemQtyDTO;
    }
}
