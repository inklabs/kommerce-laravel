<?php
namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Product\AddTagToProductCommand;
use inklabs\kommerce\Exception\KommerceException;

class AddTagToProductController extends Controller
{
    public function post(Request $request)
    {
        $productId = $request->input('productId');
        $tagId = $request->input('tagId');

        try {
            $this->dispatch(new AddTagToProductCommand(
                $productId,
                $tagId
            ));

            $this->flashSuccess('Successfully added tag');
        } catch (KommerceException $e) {
            $this->flashError('Unable to add tag');
        }

        return redirect()->route(
            'admin.product.tags',
            [
                'productId' => $productId,
            ]
        );
    }
}
