<?php
namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Lib\Arr;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\TaxRate\UpdateZip5TaxRateCommand;
use inklabs\kommerce\Exception\EntityValidatorException;

class UpdateZipcodeSalesTaxRulesController extends Controller
{
    public function post(Request $request)
    {
        $taxRate = $request->input('taxRate');
        $taxRateId = $request->input('taxRateId');
        $rate = (float) Arr::get($taxRate, 'rate');
        $applyToShipping = (bool) Arr::get($taxRate, 'applyToShipping', false);
        $zip5 = Arr::get($taxRate, 'zip5');
        try {
            $this->dispatch(new UpdateZip5TaxRateCommand(
                $taxRateId,
                $zip5,
                $rate,
                $applyToShipping
            ));

        $this->flashSuccess('Tax rate has been updated.');

        } catch (EntityValidatorException $e) {
            $this->flashError('Unable to update tax rate!');
            $this->flashFormErrors($e->getErrors());
        }

        return redirect()->route('admin.settings.sales-tax.zipcode');
    }
}
