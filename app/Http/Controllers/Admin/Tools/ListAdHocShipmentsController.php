<?php
namespace App\Http\Controllers\Admin\Tools;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Shipment\ListAdHocShipmentsQuery;
use inklabs\kommerce\Action\Shipment\Query\ListAdHocShipmentsRequest;
use inklabs\kommerce\Action\Shipment\Query\ListAdHocShipmentsResponse;

class ListAdHocShipmentsController extends Controller
{
    public function index(Request $httpRequest)
    {
        $queryString = $httpRequest->query('q');

        $request = new ListAdHocShipmentsRequest(
            $queryString,
            $this->getPaginationDTO(20)
        );

        $response = new ListAdHocShipmentsResponse();
        $this->dispatchQuery(new ListAdHocShipmentsQuery($request, $response));

        $shipmentTrackers = $response->getShipmentTrackerDTOs();
        $pagination = $response->getPaginationDTO();

        return $this->renderTemplate(
            '@theme/admin/tools/ad-hoc-shipment/index.twig',
            [
                'shipmentTrackers' => $shipmentTrackers,
                'pagination' => $pagination,
                'queryString' => $queryString,
            ]
        );
    }
}
