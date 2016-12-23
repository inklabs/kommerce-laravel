<?php
namespace App\Http\Controllers\Admin\Option;

use App\Http\Controllers\Controller;

class ListOptionProductsController extends Controller
{
    public function index($optionId)
    {
        $option = $this->getOptionWithAllData($optionId);

        return $this->renderTemplate(
            '@theme/admin/option/option-products.twig',
            [
                'option' => $option,
            ]
        );
    }
}
