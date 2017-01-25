<?php
namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;

class EditShippingConfigurationController extends Controller
{
    public function get()
    {
        $configuration = [
            'easypostApiKey' => [
                'isActive' => true,
                'value' => 'xxx',
                'created' => new \DateTime(),
                'updated' => new \DateTime(),
            ],
        ];

        return $this->renderTemplate(
            '@theme/admin/settings/store/shipping.twig',
            [
                'configuration' => $configuration,
            ]
        );
    }
}
