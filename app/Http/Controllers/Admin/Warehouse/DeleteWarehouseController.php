<?php
namespace App\Http\Controllers\Admin\Warehouse;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Warehouse\DeleteWarehouseCommand;
use inklabs\kommerce\Exception\KommerceException;

class DeleteWarehouseController extends Controller
{
    public function post(Request $request)
    {
        $warehouseId = $request->input('warehouseId');

        try {
            $this->dispatch(new DeleteWarehouseCommand($warehouseId));
            $this->flashSuccess('Success removing warehouse');
        } catch (KommerceException $e) {
            $this->flashError('Unable remove warehouse.');
        }

        return redirect()->route('admin.warehouse');
    }
}
