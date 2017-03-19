<?php
namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use inklabs\kommerce\Action\TaxRate\ListTaxRatesQuery;
use inklabs\kommerce\ActionResponse\TaxRate\ListTaxRatesResponse;
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
        /** @var ListTaxRatesResponse $response */
        $response = $this->dispatchQuery(new ListTaxRatesQuery());

        foreach ($response->getTaxRateDTOs() as $taxRate) {
            if ($taxRate->state !== null) {
                yield $taxRate;
            }
        }
    }
}
