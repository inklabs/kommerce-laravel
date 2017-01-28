<?php
namespace App\Http\Controllers\Admin\Tools;

use App\Http\Controllers\Controller;
use App\Lib\Arr;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Shipment\BuyAdHocShipmentLabelCommand;
use inklabs\kommerce\Action\Shipment\GetShipmentRatesQuery;
use inklabs\kommerce\Action\Shipment\Query\GetShipmentRatesRequest;
use inklabs\kommerce\Action\Shipment\Query\GetShipmentRatesResponse;
use inklabs\kommerce\EntityDTO\OrderAddressDTO;
use inklabs\kommerce\EntityDTO\ParcelDTO;
use inklabs\kommerce\Exception\EntityValidatorException;

class CreateAdHocShipmentController extends Controller
{
    public function get()
    {
        $fromAddress = $this->getStoreAddress();

        return $this->renderTemplate(
            '@admin/tools/ad-hoc-shipment/new.twig',
            [
                'fromAddress' => $fromAddress,
            ]
        );
    }

    public function post(Request $request)
    {
        $shipment = $request->input('shipment');
        $weightLbs = $this->getFloatOrNull($request->input('shipment.weightLbs'));
        $length = $this->getIntOrNull($request->input('shipment.length'));
        $width = $this->getIntOrNull($request->input('shipment.width'));
        $height = $this->getIntOrNull($request->input('shipment.height'));
        $weightOz = (int) round($weightLbs * 16);

        $fromAddress = $request->input('fromAddress');
        $toAddress = $request->input('toAddress');

        $fromAddressDTO = $this->getOrderAddressDTOFromArray($fromAddress);
        $toAddressDTO = $this->getOrderAddressDTOFromArray($toAddress);

        $parcelDTO = new ParcelDTO();
        $parcelDTO->length = $length;
        $parcelDTO->width = $width;
        $parcelDTO->height = $height;
        $parcelDTO->weight = $weightOz;

        $request = new GetShipmentRatesRequest($toAddressDTO, $parcelDTO, $fromAddressDTO);
        $response = new GetShipmentRatesResponse();
        $this->dispatchQuery(new GetShipmentRatesQuery($request, $response));

        $shipmentRates = $response->getShipmentRateDTOs();

        return $this->renderTemplate(
            '@admin/tools/ad-hoc-shipment/new.twig',
            [
                'shipment' => $shipment,
                'fromAddress' => $fromAddress,
                'toAddress' => $toAddress,
                'shipmentRates' => $shipmentRates,
            ]
        );
    }

    public function postBuyShipmentLabel(Request $httpRequest)
    {
        $shipmentExternalId = $httpRequest->input('shipmentExternalId');
        $shipmentRateExternalId = $httpRequest->input('shipmentRateExternalId');

        try {
            $command = new BuyAdHocShipmentLabelCommand(
                $shipmentExternalId,
                $shipmentRateExternalId
            );

            $this->dispatch($command);

            $this->flashSuccess('Created Shipping Label.');
        } catch (EntityValidatorException $e) {
            $this->flashGenericWarning();
        }

        return redirect()->route(
            'admin.tools.ad-hoc-shipment.view',
            [
                'shipmentTrackerId' => $command->getShipmentTrackerId()->getHex(),
            ]
        );
    }

    /**
     * @param $address
     * @return OrderAddressDTO
     */
    private function getOrderAddressDTOFromArray($address)
    {
        $addressDTO = new OrderAddressDTO();
        $addressDTO->firstName = Arr::get($address, 'firstName');
        $addressDTO->lastName = Arr::get($address, 'lastName');
        $addressDTO->fullName = $addressDTO->firstName . ' ' . $addressDTO->lastName;
        $addressDTO->company = $this->getStringOrNull(Arr::get($address, 'company'));
        $addressDTO->address1 = Arr::get($address, 'address1');
        $addressDTO->address2 = $this->getStringOrNull(Arr::get($address, 'address2'));
        $addressDTO->isResidential = (bool)Arr::get($address, 'isResidential');
        $addressDTO->city = Arr::get($address, 'city');
        $addressDTO->state = Arr::get($address, 'state');
        $addressDTO->zip5 = Arr::get($address, 'zip5');
        $addressDTO->phone = $this->getStringOrNull(Arr::get($address, 'phone'));
        $addressDTO->email = $this->getStringOrNull(Arr::get($address, 'email'));
        return $addressDTO;
    }

}
