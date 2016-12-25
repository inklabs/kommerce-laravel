<?php
namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Lib\Arr;
use Exception;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\TaxRate\CreateStateTaxRateCommand;
use inklabs\kommerce\Action\TaxRate\CreateZip5RangeTaxRateCommand;
use inklabs\kommerce\Action\TaxRate\CreateZip5TaxRateCommand;
use inklabs\kommerce\Action\TaxRate\DeleteTaxRateCommand;
use inklabs\kommerce\Exception\EntityValidatorException;
use inklabs\kommerce\Exception\KommerceException;

class EditTaxRateController extends Controller
{
    public function postNew(Request $request)
    {
        $type = $request->input('type');

        $taxRate = $request->input('taxRate');
        $rate = (float) Arr::get($taxRate, 'rate');
        $applyToShipping = (bool) Arr::get($taxRate, 'applyToShipping', false);

        try {
            if ($type === 'zip5TaxRate') {
                $zip5 = Arr::get($taxRate, 'zip5');

                $this->dispatch(new CreateZip5TaxRateCommand(
                    $zip5,
                    $rate,
                    $applyToShipping
                ));

            } elseif ($type === 'zip5RangeTaxRate') {
                // TODO CreateZip5RangeTaxRateCommand
                $zip5From = Arr::get($taxRate, 'zip5From');
                $zip5To = Arr::get($taxRate, 'zip5To');

                $this->dispatch(new CreateZip5RangeTaxRateCommand(
                    $zip5From,
                    $zip5To,
                    $rate,
                    $applyToShipping
                ));

            } elseif ($type === 'stateTaxRate') {
                $state = Arr::get($taxRate, 'state');

                $this->dispatch(new CreateStateTaxRateCommand(
                    $state,
                    $rate,
                    $applyToShipping
                ));
            } else {
                throw new Exception();
            }
            $this->flashSuccess('Tax rate has been created.');

        } catch (EntityValidatorException $e) {
            $this->flashError('Unable to create tax rate!');
            $this->flashFormErrors($e->getErrors());
        } catch (Exception $e) {
            $this->flashError('Unable to create tax rate!');
        }

        return redirect()->route('admin.settings.sales-tax');
    }

    public function postEdit(Request $request)
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

    private function edit(Request $request)
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
