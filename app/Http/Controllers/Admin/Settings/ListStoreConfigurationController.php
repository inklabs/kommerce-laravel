<?php
namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;

class ListStoreConfigurationController extends Controller
{
    public function index()
    {
        $configurations = [
            [
                'key' => 'storeTheme',
                'name' => 'Store Theme',
                'value' => 'foundation',
                'created' => new \DateTime(),
                'updated' => new \DateTime(),
            ],
            [
                'key' => 'adminTheme',
                'name' => 'Admin Theme',
                'value' => 'cardinal',
                'created' => new \DateTime(),
                'updated' => new \DateTime(),
            ],
        ];

        return $this->renderTemplate(
            '@theme/admin/settings/store/index.twig',
            [
                'configurations' => $configurations,
            ]
        );
    }
}
