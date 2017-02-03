<?php
namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\TaxRate\ListTaxRatesQuery;
use inklabs\kommerce\Action\TaxRate\Query\ListTaxRatesRequest;
use inklabs\kommerce\Action\TaxRate\Query\ListTaxRatesResponse;
use inklabs\kommerce\Entity\TaxRate;

class ListSalesTaxRulesController extends Controller
{
    public function index()
    {
        $request = new ListTaxRatesRequest();
        $response = new ListTaxRatesResponse();
        $this->dispatchQuery(new ListTaxRatesQuery($request, $response));

        $stateTaxRates = [];
        $zip5TaxRates = [];
        $zip5RangeTaxRates = [];
        foreach ($response->getTaxRateDTOs() as $taxRate) {
            if ($taxRate->state !== null) {
                $stateTaxRates[] = $taxRate;
            } elseif ($taxRate->zip5 !== null) {
                $zip5TaxRates[] = $taxRate;
            } elseif ($taxRate->zip5From !== null) {
                $zip5RangeTaxRates[] = $taxRate;
            }
        }

        return $this->renderTemplate(
            '@admin/settings/sales-tax/index.twig',
            [
                'stateTaxRates' => $stateTaxRates,
                'zip5TaxRates' => $zip5TaxRates,
                'zip5RangeTaxRates' => $zip5RangeTaxRates,
                'validStatesMap' => TaxRate::getValidStatesMap(),
            ]
        );
    }
}
