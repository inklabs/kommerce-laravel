<?php
namespace App\Http\Controllers\Admin\Warehouse;

use App\Http\Controllers\Controller;
use App\Lib\Arr;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Warehouse\UpdateInventoryLocationCommand;
use inklabs\kommerce\Exception\EntityValidatorException;

class EditInventoryLocationController extends Controller
{
    public function get($inventoryLocationId)
    {
        $inventoryLocation = $this->getInventoryLocationWithAllData($inventoryLocationId);

        return $this->renderTemplate(
            '@admin/warehouse/inventory-location/edit.twig',
            [
                'inventoryLocation' => $inventoryLocation,
            ]
        );
    }

    public function post(Request $request)
    {
        $inventoryLocationId = $request->input('inventoryLocationId');

        $inventoryLocation = $request->input('inventoryLocation');
        $name = trim(Arr::get($inventoryLocation, 'name'));
        $code = trim(Arr::get($inventoryLocation, 'code'));

        try {
            $this->dispatch(new UpdateInventoryLocationCommand(
                $name,
                $code,
                $inventoryLocationId
            ));

            $this->flashSuccess('Inventory location has been saved.');
            return redirect()->route(
                'admin.warehouse.inventory-location.edit',
                [
                    'inventoryLocationId' => $inventoryLocationId,
                ]
            );
        } catch (EntityValidatorException $e) {
            $this->flashError('Unable to save inventory location!');
            $this->flashFormErrors($e->getErrors());
        }

        return $this->get($inventoryLocationId);
    }
}
