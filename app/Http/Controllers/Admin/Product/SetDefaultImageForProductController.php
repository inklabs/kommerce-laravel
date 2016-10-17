<?php
namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Product\SetDefaultImageForProductCommand;
use inklabs\kommerce\Exception\KommerceException;

class SetDefaultImageForProductController extends Controller
{
    public function post(Request $request)
    {
        $productId = $request->input('productId');
        $imageId = $request->input('imageId');

        try {
            $this->dispatch(new SetDefaultImageForProductCommand(
                $productId,
                $imageId
            ));

            $this->flashSuccess('Success updating default image.');
        } catch (KommerceException $e) {
            $this->flashError('Unable to update default image!');
        }

        return redirect()->route(
            'admin.product.images',
            [
                'productId' => $productId,
            ]
        );
    }
}
