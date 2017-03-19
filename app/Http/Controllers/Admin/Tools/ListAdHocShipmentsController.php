<?php
namespace App\Http\Controllers\Admin\Tools;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Shipment\ListAdHocShipmentsQuery;
use inklabs\kommerce\ActionResponse\Shipment\ListAdHocShipmentsResponse;

class ListAdHocShipmentsController extends Controller
{
    public function index(Request $httpRequest)
    {
        $queryString = $httpRequest->query('q');

        /** @var ListAdHocShipmentsResponse $response */
        $response = $this->dispatchQuery(new ListAdHocShipmentsQuery(
            $queryString,
            $this->getPaginationDTO(20)
        ));

        $shipmentTrackers = $response->getShipmentTrackerDTOs();
        $pagination = $response->getPaginationDTO();

        return $this->renderTemplate(
            '@admin/tools/ad-hoc-shipment/index.twig',
            [
                'shipmentTrackers' => $shipmentTrackers,
                'pagination' => $pagination,
                'queryString' => $queryString,
            ]
        );
    }
}
