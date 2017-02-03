<?php
namespace App\Http\Controllers\Admin\Tag;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Product\RemoveTagFromProductCommand;
use inklabs\kommerce\Exception\KommerceException;

class RemoveProductFromTagController extends Controller
{
    public function post(Request $request)
    {
        $tagId = $request->input('tagId');
        $productId = $request->input('productId');

        try {
            $this->dispatch(new RemoveTagFromProductCommand(
                $productId,
                $tagId
            ));

            $this->flashSuccess('Successfully removed product');
        } catch (KommerceException $e) {
            $this->flashError('Unable to remove product');
        }

        return redirect()->route(
            'admin.tag.products',
            [
                'tagId' => $tagId,
            ]
        );
    }
}
