<?php
namespace App\Http\Controllers\Admin\Warehouse;

use App\Http\Controllers\Controller;
use inklabs\kommerce\Action\Warehouse\ListProductStockForInventoryLocationQuery;
use inklabs\kommerce\ActionResponse\Warehouse\ListProductStockForInventoryLocationResponse;

class ListProductsForInventoryLocationController extends Controller
{
    public function index($inventoryLocationId)
    {
        $inventoryLocation = $this->getInventoryLocationWithAllData($inventoryLocationId);

        /** @var ListProductStockForInventoryLocationResponse $response */
        $response = $this->dispatchQuery(new ListProductStockForInventoryLocationQuery(
            $inventoryLocationId,
            $this->getPaginationDTO(20)
        ));

        return $this->renderTemplate(
            '@admin/warehouse/inventory-location/products.twig',
            [
                'inventoryLocation' => $inventoryLocation,
                'productStockDTOs' => $response->getProductStockDTOs(),
            ]
        );
    }
}
