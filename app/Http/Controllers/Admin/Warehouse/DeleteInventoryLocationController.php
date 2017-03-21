<?php
namespace App\Http\Controllers\Admin\Warehouse;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Warehouse\DeleteInventoryLocationCommand;
use inklabs\kommerce\Exception\KommerceException;

class DeleteInventoryLocationController extends Controller
{
    public function post(Request $request)
    {
        $warehouseId = $request->input('warehouseId');
        $inventoryLocationId = $request->input('inventoryLocationId');

        try {
            $this->dispatch(new DeleteInventoryLocationCommand($inventoryLocationId));

            $this->flashSuccess('Success deleting inventory location.');
        } catch (KommerceException $e) {
            $this->flashError('Unable to delete inventory location!');
        }

        return redirect()->route(
            'admin.warehouse.inventory-locations',
            [
                'warehouseId' => $warehouseId,
            ]
        );
    }
}
