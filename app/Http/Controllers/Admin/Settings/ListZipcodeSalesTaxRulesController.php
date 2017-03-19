<?php
namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use inklabs\kommerce\Action\TaxRate\ListTaxRatesQuery;
use inklabs\kommerce\ActionResponse\TaxRate\ListTaxRatesResponse;

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
        /** @var ListTaxRatesResponse $response */
        $response = $this->dispatchQuery(new ListTaxRatesQuery());

        foreach ($response->getTaxRateDTOs() as $taxRate) {
            if ($taxRate->zip5 !== null) {
                yield $taxRate;
            }
        }
    }
}
