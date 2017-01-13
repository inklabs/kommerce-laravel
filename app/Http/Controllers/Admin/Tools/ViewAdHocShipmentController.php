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

class ViewAdHocShipmentController extends Controller
{
    public function get($shipmentTrackerId)
    {
        $shipmentTracker = $this->getShipmentTracker($shipmentTrackerId);

        return $this->renderTemplate(
            '@theme/admin/tools/ad-hoc-shipment/view.twig',
            [
                'shipmentTracker' => $shipmentTracker
            ]
        );
    }
}
