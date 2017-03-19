<?php
namespace App\Http\Controllers\Admin\Warehouse;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Warehouse\ListWarehousesQuery;
use inklabs\kommerce\ActionResponse\Warehouse\ListWarehousesResponse;

class ListWarehousesController extends Controller
{
    public function index(Request $httpRequest)
    {
        $queryString = $httpRequest->query('q');

        /** @var ListWarehousesResponse $response */
        $response = $this->dispatchQuery(new ListWarehousesQuery(
            $queryString,
            $this->getPaginationDTO(20)
        ));

        $warehouses = $response->getWarehouseDTOs();
        $pagination = $response->getPaginationDTO();

        return $this->renderTemplate(
            '@admin/warehouse/index.twig',
            [
                'warehouses' => $warehouses,
                'pagination' => $pagination,
                'queryString' => $queryString,
            ]
        );
    }
}
