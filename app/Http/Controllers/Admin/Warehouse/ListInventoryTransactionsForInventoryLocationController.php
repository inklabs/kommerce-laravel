<?php
namespace App\Http\Controllers\Admin\Warehouse;

use App\Http\Controllers\Controller;
use inklabs\kommerce\Action\Warehouse\ListInventoryTransactionsByInventoryLocationQuery;
use inklabs\kommerce\ActionResponse\Warehouse\ListInventoryTransactionsByInventoryLocationResponse;

class ListInventoryTransactionsForInventoryLocationController extends Controller
{
    public function index($inventoryLocationId)
    {
        $inventoryLocation = $this->getInventoryLocationWithAllData($inventoryLocationId);

        /** @var ListInventoryTransactionsByInventoryLocationResponse $response */
        $response = $this->dispatchQuery(new ListInventoryTransactionsByInventoryLocationQuery(
            $inventoryLocationId,
            $this->getPaginationDTO(20)
        ));

        return $this->renderTemplate(
            '@admin/warehouse/inventory-location/inventory-transactions.twig',
            [
                'inventoryLocation' => $inventoryLocation,
                'inventoryTransactions' => $response->getInventoryTransactionDTOs(),
            ]
        );
    }
}
