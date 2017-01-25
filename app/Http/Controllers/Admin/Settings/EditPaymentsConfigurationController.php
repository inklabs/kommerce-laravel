<?php
namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;

class EditPaymentsConfigurationController extends Controller
{
    public function get()
    {
        $configuration = [
            'stripeApiKey' => [
                'isActive' => true,
                'value' => 'xxx',
                'created' => new \DateTime(),
                'updated' => new \DateTime(),
            ],
        ];

        return $this->renderTemplate(
            '@theme/admin/settings/store/payments.twig',
            [
                'configuration' => $configuration,
            ]
        );
    }
}
