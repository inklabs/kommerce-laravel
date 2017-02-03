<?php
namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use inklabs\kommerce\Action\TaxRate\ListTaxRatesQuery;
use inklabs\kommerce\Action\TaxRate\Query\ListTaxRatesRequest;
use inklabs\kommerce\Action\TaxRate\Query\ListTaxRatesResponse;

class ListZipcodeRangeSalesTaxRulesController extends Controller
{
    public function index()
    {
        $zip5RangeTaxRates = $this->getZipcodeRangeSalesTaxRules();

        return $this->renderTemplate(
            '@admin/settings/sales-tax/zipcode-range.twig',
            [
                'zip5RangeTaxRates' => $zip5RangeTaxRates,
            ]
        );
    }

    private function getZipcodeRangeSalesTaxRules()
    {
        $request = new ListTaxRatesRequest();
        $response = new ListTaxRatesResponse();
        $this->dispatchQuery(new ListTaxRatesQuery($request, $response));

        foreach ($response->getTaxRateDTOs() as $taxRate) {
            if ($taxRate->zip5From !== null) {
                yield $taxRate;
            }
        }
    }
}
