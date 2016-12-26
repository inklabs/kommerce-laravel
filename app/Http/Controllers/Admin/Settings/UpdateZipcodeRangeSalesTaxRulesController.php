<?php
namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Lib\Arr;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\TaxRate\UpdateZip5RangeTaxRateCommand;
use inklabs\kommerce\Exception\EntityValidatorException;

class UpdateZipcodeRangeSalesTaxRulesController extends Controller
{
    public function post(Request $request)
    {
        $taxRate = $request->input('taxRate');
        $taxRateId = $request->input('taxRateId');
        $rate = (float) Arr::get($taxRate, 'rate');
        $applyToShipping = (bool) Arr::get($taxRate, 'applyToShipping', false);
        $zip5From = Arr::get($taxRate, 'zip5From');
        $zip5To = Arr::get($taxRate, 'zip5To');
        try {
            $this->dispatch(new UpdateZip5RangeTaxRateCommand(
                $taxRateId,
                $zip5From,
                $zip5To,
                $rate,
                $applyToShipping
            ));

        $this->flashSuccess('Tax rate has been updated.');

        } catch (EntityValidatorException $e) {
            $this->flashError('Unable to update tax rate!');
            $this->flashFormErrors($e->getErrors());
        }

        return redirect()->route('admin.settings.sales-tax.zipcode-range');
    }
}
