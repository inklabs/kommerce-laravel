<?php
namespace App\Http\Controllers\User\Attachment;

use App\Http\Controllers\Controller;
use App\Lib\Arr;
use Illuminate\Http\Request;
use inklabs\kommerce\Exception\KommerceException;
use inklabs\kommerce\Action\Attachment\CreateAttachmentForOrderItemCommand;
use inklabs\kommerce\EntityDTO\UploadFileDTO;

class CreateAttachmentForOrderItemController extends Controller
{
    public function get($orderItemId)
    {
        $orderItem = $this->getOrderItem($orderItemId);

        return $this->renderTemplate(
            'user/attachment/create-for-order-item.twig',
            [
                'orderItem' => $orderItem,
            ]
        );
    }

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

            return redirect()->route('user.account.view-order', ['orderId' => $orderItem->order->id->getHex()]);
        } catch (KommerceException $e) {
            $this->flashError('Unable to upload attachment.');
            return redirect()->route('user.attachment.createForOrderItem', ['orderItemId' => $orderItem->id->getHex()]);
        }
    }
}
