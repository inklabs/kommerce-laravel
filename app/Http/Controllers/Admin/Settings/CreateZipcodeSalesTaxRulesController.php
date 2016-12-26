<?php
namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Lib\Arr;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\TaxRate\CreateZip5TaxRateCommand;
use inklabs\kommerce\Exception\EntityValidatorException;

class CreateZipcodeSalesTaxRulesController extends Controller
{
    public function post(Request $request)
    {
        $taxRate = $request->input('taxRate');
        $rate = (float) Arr::get($taxRate, 'rate');
        $applyToShipping = (bool) Arr::get($taxRate, 'applyToShipping', false);
        $zip5 = Arr::get($taxRate, 'zip5');

        try {
            $this->dispatch(new CreateZip5TaxRateCommand(
                $zip5,
                $rate,
                $applyToShipping
            ));

            $this->flashSuccess('Tax rate has been created.');

        } catch (EntityValidatorException $e) {
            $this->flashError('Unable to create tax rate!');
            $this->flashFormErrors($e->getErrors());
        }

        return redirect()->route('admin.settings.sales-tax.zipcode');
    }
}
