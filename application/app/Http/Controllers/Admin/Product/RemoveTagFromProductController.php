<?php
namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Product\RemoveTagFromProductCommand;
use inklabs\kommerce\Exception\KommerceException;

class RemoveTagFromProductController extends Controller
{
    public function post(Request $request)
    {
        $productId = $request->input('productId');
        $tagId = $request->input('tagId');

        try {
            $this->dispatch(new RemoveTagFromProductCommand(
                $productId,
                $tagId
            ));

            $this->flashSuccess('Successfully removed tag');
        } catch (KommerceException $e) {
            $this->flashError('Unable to remove tag');
        }

        return redirect()->route(
            'admin.product.tags',
            [
                'productId' => $productId,
            ]
        );
    }
}
