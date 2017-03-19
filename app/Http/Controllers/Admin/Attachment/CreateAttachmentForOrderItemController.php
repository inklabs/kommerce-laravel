<?php
namespace App\Http\Controllers\Admin\Attachment;

use App\Http\Controllers\Controller;
use App\Lib\Arr;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Attachment\CreateAttachmentForOrderItemCommand;
use inklabs\kommerce\EntityDTO\UploadFileDTO;
use inklabs\kommerce\Exception\KommerceException;

class CreateAttachmentForOrderItemController extends Controller
{
    public function post(Request $request)
    {
        $orderItemId = $request->input('orderItemId');
        $orderItem = $this->getOrderItemWithAllData($orderItemId);

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
            $this->dispatch(new CreateAttachmentForOrderItemCommand(
                $uploadFileDTO,
                $orderItem->id->getHex()
            ));

            $this->flashSuccess('Attachment uploaded.');
        } catch (KommerceException $e) {
            $this->flashError('Unable to upload attachment.');
        }

        return redirect()->route('admin.order.view', ['orderId' => $orderItem->order->id->getHex()]);
    }
}
