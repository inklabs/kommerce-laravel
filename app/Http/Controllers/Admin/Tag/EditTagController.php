<?php
namespace App\Http\Controllers\Admin\Tag;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EditTagController extends Controller
{
    public function get($tagId)
    {
        $tag = $this->getTagWithAllData($tagId);

        return $this->renderTemplate(
            'admin/tag/edit.twig',
            [
                'tag' => $tag,
            ]
        );
    }
}
