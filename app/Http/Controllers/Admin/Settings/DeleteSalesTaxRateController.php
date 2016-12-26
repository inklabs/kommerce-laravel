<?php
namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\TaxRate\DeleteTaxRateCommand;
use inklabs\kommerce\Exception\KommerceException;

class DeleteSalesTaxRateController extends Controller
{
    public function post(Request $request)
    {
        $taxRateId = $request->input('taxRateId');
        $location = $request->input('location', 'admin.settings.sales-tax');

        try {
            $this->dispatch(new DeleteTaxRateCommand($taxRateId));
            $this->flashSuccess('Success removing tax rate');
        } catch (KommerceException $e) {
            $this->flashError('Unable remove tax rate.');
            $this->flashError($e->getMessage());
        }

        return redirect()->route($location);
    }
}
