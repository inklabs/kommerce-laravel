<?php
namespace App\Http\Controllers\Admin\Option;

use App\Http\Controllers\Controller;

class ListTagsForOptionController extends Controller
{
    public function index($optionId)
    {
        $option = $this->getOptionWithAllData($optionId);

        return $this->renderTemplate(
            '@admin/option/tags.twig',
            [
                'option' => $option,
            ]
        );
    }
}
