<?php
namespace App\Http\Controllers\Admin\Warehouse;

use App\Http\Controllers\Controller;

class ListWarehouseInventoryLocationsController extends Controller
{
    public function index($warehouseId)
    {
        $warehouse = $this->getWarehouseWithAllData($warehouseId);

        return $this->renderTemplate(
            '@admin/warehouse/inventory-locations.twig',
            [
                'warehouse' => $warehouse,
            ]
        );
    }
}
