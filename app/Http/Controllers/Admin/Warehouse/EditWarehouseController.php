<?php
namespace App\Http\Controllers\Admin\Warehouse;

use App\Http\Controllers\Controller;
use App\Lib\Arr;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Warehouse\UpdateWarehouseCommand;
use inklabs\kommerce\Exception\EntityValidatorException;

class EditWarehouseController extends Controller
{
    public function get($warehouseId)
    {
        $warehouse = $this->getWarehouseWithAllData($warehouseId);

        return $this->renderTemplate(
            '@admin/warehouse/edit.twig',
            [
                'warehouse' => $warehouse,
            ]
        );
    }

    public function post(Request $request)
    {
        $warehouseId = $request->input('warehouseId');
        $warehouseValues = $request->input('warehouse');
        $addressValues = Arr::get($warehouseValues, 'address');
        $pointValues = Arr::get($addressValues, 'point');

        $name = trim(Arr::get($warehouseValues, 'name'));
        $attention = trim(Arr::get($addressValues, 'attention'));
        $company = trim(Arr::get($addressValues, 'company'));
        $address1 = trim(Arr::get($addressValues, 'address1'));
        $address2 = trim(Arr::get($addressValues, 'address2'));
        $city = trim(Arr::get($addressValues, 'city'));
        $state = trim(Arr::get($addressValues, 'state'));
        $zip5 = trim(Arr::get($addressValues, 'zip5'));
        $zip4 = trim(Arr::get($addressValues, 'zip4'));
        $latitude = trim(Arr::get($pointValues, 'latitude'));
        $longitude = trim(Arr::get($pointValues, 'longitude'));

        try {
            $this->dispatch(new UpdateWarehouseCommand(
                $name,
                $attention,
                $company,
                $address1,
                $address2,
                $city,
                $state,
                $zip5,
                $zip4,
                $latitude,
                $longitude,
                $warehouseId
            ));

            $this->flashSuccess('Warehouse has been saved.');
            return redirect()->route(
                'admin.warehouse.edit',
                [
                    'warehouseId' => $warehouseId,
                ]
            );
        } catch (EntityValidatorException $e) {
            $this->flashError('Unable to save warehouse!');
            $this->flashFormErrors($e->getErrors());
        }

        return $this->get($warehouseId);
    }
}
