<?php
namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Product\DeleteProductCommand;
use inklabs\kommerce\Exception\KommerceException;

class DeleteProductController extends Controller
{
    public function post(Request $request)
    {
        $productId = $request->input('productId');

        try {
            $this->dispatch(new DeleteProductCommand($productId));
            $this->flashSuccess('Success removing product');
        } catch (KommerceException $e) {
            $this->flashError('Unable remove product.');
        }

        return redirect()->route('admin.product');
    }
}
