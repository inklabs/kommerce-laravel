<?php
namespace App\Http\Controllers\Admin\Option;

use App\Http\Controllers\Controller;

class ListOptionValuesController extends Controller
{
    public function index($optionId)
    {
        $option = $this->getOptionWithAllData($optionId);

        return $this->renderTemplate(
            '@theme/admin/option/values.twig',
            [
                'option' => $option,
            ]
        );
    }
}
