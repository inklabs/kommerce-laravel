<?php
namespace App\Http\Controllers\Admin\Attachment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Attachment\DeleteAttachmentCommand;
use inklabs\kommerce\Exception\KommerceException;

class DeleteAttachmentController extends Controller
{
    public function post(Request $request)
    {
        $attachmentId = $request->input('attachmentId');
        $orderId = $request->input('orderId');

        try {
            $this->dispatch(new DeleteAttachmentCommand($attachmentId));
            $this->flashSuccess('Success removing attachment');
        } catch (KommerceException $e) {
            $this->flashError('Unable remove attachment.');
        }

        return redirect()->route(
            'admin.order.view',
            [
                'orderId' => $orderId
            ]
        );
    }
}
