<?php
namespace App\Http\Controllers\Admin\Warehouse;

use App\Http\Controllers\Controller;
use App\Lib\Arr;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Warehouse\CreateWarehouseCommand;
use inklabs\kommerce\Exception\EntityValidatorException;

class CreateWarehouseController extends Controller
{
    public function get()
    {
        return $this->renderTemplate('@admin/warehouse/new.twig');
    }

    public function post(Request $request)
    {
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
            $command = new CreateWarehouseCommand(
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
                $longitude
            );

            $this->dispatch($command);

            $this->flashSuccess('Warehouse has been created.');
            return redirect()->route(
                'admin.warehouse.edit',
                [
                    'warehouseId' => $command->getWarehouseId()->getHex(),
                ]
            );
        } catch (EntityValidatorException $e) {
            $this->flashError('Unable to create warehouse!');
            $this->flashFormErrors($e->getErrors());
        }

        return $this->get();
    }
}
