<?php
namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use inklabs\kommerce\Action\TaxRate\ListTaxRatesQuery;
use inklabs\kommerce\Action\TaxRate\Query\ListTaxRatesRequest;
use inklabs\kommerce\Action\TaxRate\Query\ListTaxRatesResponse;

class ListZipcodeSalesTaxRulesController extends Controller
{
    public function index()
    {
        $zip5TaxRates = $this->getZipcodeSalesTaxRules();

        return $this->renderTemplate(
            '@admin/settings/sales-tax/zipcode.twig',
            [
                'zip5TaxRates' => $zip5TaxRates,
            ]
        );
    }

    private function getZipcodeSalesTaxRules()
    {
        $request = new ListTaxRatesRequest();
        $response = new ListTaxRatesResponse();
        $this->dispatchQuery(new ListTaxRatesQuery($request, $response));

        foreach ($response->getTaxRateDTOs() as $taxRate) {
            if ($taxRate->zip5 !== null) {
                yield $taxRate;
            }
        }
    }
}
