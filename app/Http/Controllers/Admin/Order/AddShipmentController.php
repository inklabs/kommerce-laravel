<?php
namespace App\Http\Controllers\Admin\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Shipment\AddShipmentTrackingCodeCommand;
use inklabs\kommerce\Action\Shipment\BuyShipmentLabelCommand;
use inklabs\kommerce\Action\Shipment\GetShipmentRatesQuery;
use inklabs\kommerce\ActionResponse\Shipment\GetShipmentRatesResponse;
use inklabs\kommerce\Entity\ShipmentCarrierType;
use inklabs\kommerce\EntityDTO\OrderItemQtyDTO;
use inklabs\kommerce\EntityDTO\ParcelDTO;
use inklabs\kommerce\Exception\EntityValidatorException;
use inklabs\kommerce\Lib\Uuid;

class AddShipmentController extends Controller
{
    public function get($orderId)
    {
        $order = $this->getOrderWithAllData($orderId);

        return $this->renderTemplate(
            '@admin/order/add-shipment.twig',
            [
                'order' => $order,
            ]
        );
    }

    public function post(Request $request)
    {
        $action = $request->input('action');

        if ($action === 'create-with-tracking-code') {
            return $this->createWithTrackingCode($request);
        } elseif ($action === 'create-shipping-label') {
            return $this->createWithShippingLabel($request);
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

    public function postBuyShipmentLabel(Request $httpRequest)
    {
        $orderId = $httpRequest->input('orderId');
        $orderItemQty = $httpRequest->input('orderItemQty');
        $comment = $httpRequest->input('comment');
        $shipmentExternalId = $httpRequest->input('shipmentExternalId');
        $shipmentRateExternalId = $httpRequest->input('shipmentRateExternalId');

        $orderItemQtyDTO = $this->getOrderItemQtyDTO($orderItemQty);

        try {
            $this->dispatch(new BuyShipmentLabelCommand(
                $orderId,
                $orderItemQtyDTO,
                $comment,
                $shipmentExternalId,
                $shipmentRateExternalId
            ));

            $this->flashSuccess('Added Shipping Label.');
        } catch (EntityValidatorException $e) {
            $this->flashGenericWarning();
        }
        return redirect()->route('admin.order.shipments', ['orderId' => $orderId]);
    }

    public function postAddShipmentLabel(Request $httpRequest)
    {
        $orderId = $httpRequest->input('orderId');
        $orderItemQty = $httpRequest->input('orderItemQty');
        $comment = $httpRequest->input('comment');
        $shipment = $httpRequest->input('shipment');
        $weightLbs = $httpRequest->input('shipment.weightLbs');
        $weightOz = (int) round($weightLbs * 16);
        $length = $httpRequest->input('shipment.length');
        $width = $httpRequest->input('shipment.width');
        $height = $httpRequest->input('shipment.height');

        $order = $this->getOrderWithAllData($orderId);

        $toAddress = $order->shippingAddress;

        $parcel = new ParcelDTO;
        $parcel->length = $length;
        $parcel->width = $width;
        $parcel->height = $height;
        $parcel->weight = $weightOz;

        /** @var GetShipmentRatesResponse $response */
        $response = $this->dispatchQuery(new GetShipmentRatesQuery($toAddress, $parcel));

        $shipmentRates = $response->getShipmentRateDTOs();
        $shipment['shipmentRateExternalId'] = $shipmentRates[0]->externalId;

        return $this->renderTemplate(
            '@admin/order/add-shipment-label.twig',
            [
                'order' => $order,
                'orderItemQty' => $orderItemQty,
                'shipment' => $shipment,
                'comment' => $comment,
                'shipmentRates' => $shipmentRates,
            ]
        );
    }

    private function createWithTrackingCode(Request $request)
    {
        $orderId = $request->input('orderId');
        $orderItemQty = $request->input('orderItemQty');
        $comment = $request->input('comment');

        $order = $this->getOrderWithAllData($orderId);

        return $this->renderTemplate(
            '@admin/order/add-shipment-tracking.twig',
            [
                'order' => $order,
                'orderItemQty' => $orderItemQty,
                'comment' => $comment,
                'shipmentCarrierTypes' => ShipmentCarrierType::getNameMap(),
            ]
        );
    }

    private function createWithShippingLabel(Request $request)
    {
        $orderId = $request->input('orderId');
        $orderItemQty = $request->input('orderItemQty');
        $comment = $request->input('comment');

        $order = $this->getOrderWithAllData($orderId);

        $weightOz = 0;
        foreach ($order->orderItems as $orderItem) {
            if (array_key_exists($orderItem->id->getHex(), $orderItemQty)) {
                $weightOz += ($orderItem->shippingWeight * $orderItemQty[$orderItem->id->getHex()]);
            }
        }

        $shipment = [
            'weightLbs' => $weightOz / 16,
        ];

        return $this->renderTemplate(
            '@admin/order/add-shipment-label.twig',
            [
                'order' => $order,
                'orderItemQty' => $orderItemQty,
                'shipment' => $shipment,
                'comment' => $comment,
            ]
        );
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
