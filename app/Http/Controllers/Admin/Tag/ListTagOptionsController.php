<?php
namespace App\Http\Controllers\Admin\Tag;

use App\Http\Controllers\Controller;

class ListTagOptionsController extends Controller
{
    public function index($tagId)
    {
        $tag = $this->getTagWithAllData($tagId);

        return $this->renderTemplate(
            '@theme/admin/tag/options.twig',
            [
                'tag' => $tag,
            ]
        );
    }
}
