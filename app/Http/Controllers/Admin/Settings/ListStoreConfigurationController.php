<?php
namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;

class ListStoreConfigurationController extends Controller
{
    public function index()
    {
        $configurations = $this->getConfigurationsByKeys([
            'adminTheme',
            'storeTheme',
        ]);

        return $this->renderTemplate(
            '@admin/settings/store/index.twig',
            [
                'configurations' => $configurations,
            ]
        );
    }
}
