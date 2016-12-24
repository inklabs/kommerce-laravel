<?php
namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ListSalesTaxRulesController extends Controller
{
    public function index(Request $httpRequest)
    {
        return $this->renderTemplate('@theme/admin/settings/sales-tax/index.twig');
    }
}
