<?php
namespace App\Http\Controllers\Admin\Warehouse;

use App\Http\Controllers\Controller;
use App\Lib\Arr;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Warehouse\CreateInventoryLocationCommand;
use inklabs\kommerce\Exception\EntityValidatorException;

class CreateInventoryLocationController extends Controller
{
    public function post(Request $request)
    {
        $warehouseId = $request->input('warehouseId');
        $inventoryLocationValues = $request->input('inventoryLocation');

        $name = trim(Arr::get($inventoryLocationValues, 'name'));
        $code = trim(Arr::get($inventoryLocationValues, 'code'));

        try {
            $this->dispatch(new CreateInventoryLocationCommand(
                $warehouseId,
                $name,
                $code
            ));

            $this->flashSuccess('Successfully added inventory location');
        } catch (EntityValidatorException $e) {
            $this->flashError('Unable to add inventory location!');
            $this->flashFormErrors($e->getErrors());
        }

        return redirect()->route(
            'admin.warehouse.inventory-locations',
            [
                'warehouseId' => $warehouseId,
            ]
        );
    }
}
