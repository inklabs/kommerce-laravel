<?php
namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;

class EditPaymentsConfigurationController extends Controller
{
    public function get()
    {
        $configurations = $this->getConfigurationsByKeys([
            'stripeApiKey',
        ]);

        return $this->renderTemplate(
            '@theme/admin/settings/store/payments.twig',
            [
                'configurations' => $configurations,
            ]
        );
    }
}
