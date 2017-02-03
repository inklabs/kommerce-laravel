<?php
namespace App\Http\Controllers\User\Attachment;

use App\Http\Controllers\Controller;
use App\Lib\Arr;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Attachment\CreateAttachmentForUserProductCommand;
use inklabs\kommerce\EntityDTO\UploadFileDTO;
use inklabs\kommerce\Exception\KommerceException;

class CreateAttachmentForProductController extends Controller
{
    public function get($productId)
    {
        $product = $this->getProduct($productId);

        return $this->renderTemplate(
            '@store/user/attachment/create-for-product.twig',
            [
                'product' => $product,
            ]
        );
    }

    public function post(Request $request)
    {
        $userId = ''; // TODO: Grab authenticated userId
        $productId = $request->input('productId');
        $product = $this->getProduct($productId);

        $image = Arr::get($_FILES, 'image');

        $uploadFileDTO = new UploadFileDTO(
            Arr::get($image, 'name'),
            Arr::get($image, 'type'),
            Arr::get($image, 'tmp_name'),
            Arr::get($image, 'size')
        );

        dd(get_defined_vars());

        if (! is_uploaded_file($uploadFileDTO->getFilePath())) {
            abort(400);
        }

        try {
            $this->dispatch(new CreateAttachmentForUserProductCommand(
                $uploadFileDTO,
                $userId,
                $product->id->getHex()
            ));

            $this->flashSuccess('Attachment uploaded.');

            return redirect()->route('product.show', ['slug' => $product->slug, 'productId' => $product->id->getHex()]);
        } catch (KommerceException $e) {
            $this->flashError('Unable to upload attachment.');
            return redirect()->route('user.attachment.createForProduct', ['productId' => $product->id->getHex()]);
        }
    }
}
