<?php
namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Product\RemoveImageFromProductCommand;
use inklabs\kommerce\Exception\KommerceException;

class RemoveImageFromProductController extends Controller
{
    public function post(Request $request)
    {
        $productId = $request->input('productId');
        $imageId = $request->input('imageId');

        try {
            $this->dispatch(new RemoveImageFromProductCommand(
                $productId,
                $imageId
            ));

            $this->flashSuccess('Success removing image.');
        } catch (KommerceException $e) {
            $this->flashError('Unable to remove image!');
        }

        return redirect()->route(
            'admin.product.images',
            [
                'productId' => $productId,
            ]
        );
    }
}
