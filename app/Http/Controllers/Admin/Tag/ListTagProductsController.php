<?php
namespace App\Http\Controllers\Admin\Tag;

use App\Http\Controllers\Controller;

class ListTagProductsController extends Controller
{
    public function index($tagId)
    {
        $tag = $this->getTagWithAllData($tagId);

        return $this->renderTemplate(
            '@admin/tag/products.twig',
            [
                'tag' => $tag,
            ]
        );
    }
}
