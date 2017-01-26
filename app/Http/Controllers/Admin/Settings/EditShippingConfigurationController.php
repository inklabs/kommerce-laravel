<?php
namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;

class EditShippingConfigurationController extends Controller
{
    public function get()
    {
        $configurations = $this->getConfigurationsByKeys([
            'easyPostApiKey',
        ]);

        return $this->renderTemplate(
            '@theme/admin/settings/store/shipping.twig',
            [
                'configurations' => $configurations,
            ]
        );
    }
}
