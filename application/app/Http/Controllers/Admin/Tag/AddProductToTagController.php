<?php
namespace App\Http\Controllers\Admin\Tag;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Product\AddTagToProductCommand;
use inklabs\kommerce\Exception\KommerceException;

class AddProductToTagController extends Controller
{
    public function post(Request $request)
    {
        $tagId = $request->input('tagId');
        $productId = $request->input('productId');

        try {
            $this->dispatch(new AddTagToProductCommand(
                $productId,
                $tagId
            ));

            $this->flashSuccess('Successfully added product');
        } catch (KommerceException $e) {
            $this->flashError('Unable to add product');
        }

        return redirect()->route(
            'admin.tag.products',
            [
                'tagId' => $tagId,
            ]
        );
    }
}
