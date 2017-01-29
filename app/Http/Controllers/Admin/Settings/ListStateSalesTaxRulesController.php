<?php
namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use inklabs\kommerce\Action\TaxRate\ListTaxRatesQuery;
use inklabs\kommerce\Action\TaxRate\Query\ListTaxRatesRequest;
use inklabs\kommerce\Action\TaxRate\Query\ListTaxRatesResponse;
use inklabs\kommerce\Entity\TaxRate;

class ListStateSalesTaxRulesController extends Controller
{
    public function index()
    {
        $stateTaxRates = $this->getZipcodeSalesTaxRules();

        return $this->renderTemplate(
            '@admin/settings/sales-tax/state.twig',
            [
                'stateTaxRates' => $stateTaxRates,
                'validStatesMap' => TaxRate::getValidStatesMap(),
            ]
        );
    }

    private function getZipcodeSalesTaxRules()
    {
        $request = new ListTaxRatesRequest();
        $response = new ListTaxRatesResponse();
        $this->dispatchQuery(new ListTaxRatesQuery($request, $response));

        foreach ($response->getTaxRateDTOs() as $taxRate) {
            if ($taxRate->state !== null) {
                yield $taxRate;
            }
        }
    }
}
