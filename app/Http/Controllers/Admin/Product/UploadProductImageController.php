<?php
namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Lib\Arr;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Image\CreateImageForProductCommand;
use inklabs\kommerce\EntityDTO\UploadFileDTO;

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

        dd(get_defined_vars());
        try {
            $this->dispatch(new CreateImageForProductCommand(
                $product->id->getHex(),
                $uploadFileDTO
            ));

            return redirect()->route('admin.product.images', ['productId' => $product->id->getHex()]);
        } catch (KommerceException $e) {
            $this->flashError('Unable to upload Attachment.');
            return redirect()->route('admin.attachment.createForOrderItem', ['orderItemId' => $orderItem->id->getHex()]);
        }
    }
}
