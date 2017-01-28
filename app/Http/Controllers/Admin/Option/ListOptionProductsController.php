<?php
namespace App\Http\Controllers\Admin\Option;

use App\Http\Controllers\Controller;

class ListOptionProductsController extends Controller
{
    public function index($optionId)
    {
        $option = $this->getOptionWithAllData($optionId);

        return $this->renderTemplate(
            '@admin/option/option-products.twig',
            [
                'option' => $option,
            ]
        );
    }
}
