<?php
namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Warehouse\ListWarehousesQuery;
use inklabs\kommerce\Action\Warehouse\Query\ListWarehousesRequest;
use inklabs\kommerce\Action\Warehouse\Query\ListWarehousesResponse;

class ListWarehousesController extends Controller
{
    public function index(Request $httpRequest)
    {
        $queryString = $httpRequest->query('q');

        $request = new ListWarehousesRequest(
            $queryString,
            $this->getPaginationDTO(20)
        );

        $response = new ListWarehousesResponse();
        $this->dispatchQuery(new ListWarehousesQuery($request, $response));

        $warehouses = $response->getWarehouseDTOs();
        $pagination = $response->getPaginationDTO();

        return $this->renderTemplate(
            '@admin/inventory/warehouse/index.twig',
            [
                'warehouses' => $warehouses,
                'pagination' => $pagination,
                'queryString' => $queryString,
            ]
        );
    }
}
