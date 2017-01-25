<?php
namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;

class EditPaymentsConfigurationController extends Controller
{
    public function get()
    {
        $configurations = [
            [
                'key' => 'stripeApiKey',
                'name' => 'Stripe API Key',
                'value' => 'xxx',
                'created' => new \DateTime(),
                'updated' => new \DateTime(),
            ],
        ];

        return $this->renderTemplate(
            '@theme/admin/settings/store/payments.twig',
            [
                'configurations' => $configurations,
            ]
        );
    }
}
