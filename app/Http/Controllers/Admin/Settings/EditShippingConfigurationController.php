<?php
namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;

class EditShippingConfigurationController extends Controller
{
    public function get()
    {
        $configurations = [
            [
                'key' => 'easyPostApiKey',
                'name' => 'EasyPost API Key',
                'value' => 'xxx',
                'created' => new \DateTime(),
                'updated' => new \DateTime(),
            ],
        ];

        return $this->renderTemplate(
            '@theme/admin/settings/store/shipping.twig',
            [
                'configurations' => $configurations,
            ]
        );
    }
}
