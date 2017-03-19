<?php
namespace App\Http\Controllers\Admin\Tools;

use App\Http\Controllers\Controller;

class ViewAdHocShipmentController extends Controller
{
    public function get($shipmentTrackerId)
    {
        $shipmentTracker = $this->getShipmentTracker($shipmentTrackerId);

        return $this->renderTemplate(
            '@admin/tools/ad-hoc-shipment/view.twig',
            [
                'shipmentTracker' => $shipmentTracker
            ]
        );
    }
}
