<?php
namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Lib\Arr;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Image\CreateImageForProductCommand;
use inklabs\kommerce\EntityDTO\UploadFileDTO;
use inklabs\kommerce\Exception\KommerceException;

class UploadProductImageController extends Controller
{
    public function post(Request $request)
    {
        $productId = $request->input('productId');
        $product = $this->getProductWithAllData($productId);

        $image = Arr::get($_FILES, 'image');

        $uploadFileDTO = new UploadFileDTO(
            Arr::get($image, 'name'),
            Arr::get($image, 'type'),
            Arr::get($image, 'tmp_name'),
            Arr::get($image, 'size')
        );

        if (! is_uploaded_file($uploadFileDTO->getFilePath())) {
            abort(400);
        }

        try {
            $this->dispatch(new CreateImageForProductCommand(
                $uploadFileDTO,
                $product->id->getHex()
            ));
            $this->flashSuccess('Image uploaded.');
        } catch (KommerceException $e) {
            $this->flashError('Unable to upload image.');
        }
        return redirect()->route('admin.product.images', ['productId' => $product->id->getHex()]);
    }
}
