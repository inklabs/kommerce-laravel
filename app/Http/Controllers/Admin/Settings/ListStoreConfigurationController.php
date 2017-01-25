<?php
namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;

class ListStoreConfigurationController extends Controller
{
    public function index()
    {
        return $this->renderTemplate('@theme/admin/settings/store/index.twig');
    }
}
