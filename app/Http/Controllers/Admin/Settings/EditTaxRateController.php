<?php
namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\TaxRate\DeleteTaxRateCommand;
use inklabs\kommerce\Exception\KommerceException;

class EditTaxRateController extends Controller
{
    public function post(Request $request)
    {
        $action = $request->input('action');
        $taxRateId = $request->input('taxRateId');

        if ($action === 'delete') {
            return $this->delete($taxRateId);
        } elseif ($action === 'edit') {
            return $this->edit($request);
        }

        $this->flashGenericWarning();
        return redirect()->route('admin.settings.sales-tax');
    }

    private function delete($taxRateId)
    {
        try {
            $this->dispatch(new DeleteTaxRateCommand($taxRateId));
            $this->flashSuccess('Success removing tax rate');
        } catch (KommerceException $e) {
            $this->flashError('Unable remove tax rate.');
            $this->flashError($e->getMessage());
        }

        return redirect()->route('admin.settings.sales-tax');
    }

    public function edit(Request $request)
    {
        $type = $request->input('type');

        $rate = (float) $request->input('rate');
        $applyToShipping = (bool) $request->input('applyToShipping', false);

        if ($type === 'zip5TaxRate') {
            // TODO UpdateZip5TaxRateCommand
            $zip5 = $request->input('zip5');
        } elseif ($type = 'zip5RangeTaxRate') {
            // TODO UpdateZip5RangeTaxRateCommand
            $zip5From = $request->input('zip5From');
            $zip5To = $request->input('zip5To');
        } elseif ($type = 'stateTaxRate') {
            // TODO UpdateStateTaxRateCommand
            $state = $request->input('state');
        }
        echo '<pre>';
        print_r($request->input());
    }
}
