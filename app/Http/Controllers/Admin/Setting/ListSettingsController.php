<?php
namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ListSettingsController extends Controller
{
    public function index(Request $httpRequest)
    {
        return $this->renderTemplate('@theme/admin/setting/index.twig');
    }
}
