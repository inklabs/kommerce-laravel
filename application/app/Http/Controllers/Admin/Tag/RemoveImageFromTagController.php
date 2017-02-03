<?php
namespace App\Http\Controllers\Admin\Tag;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Tag\RemoveImageFromTagCommand;
use inklabs\kommerce\Exception\KommerceException;

class RemoveImageFromTagController extends Controller
{
    public function post(Request $request)
    {
        $tagId = $request->input('tagId');
        $imageId = $request->input('imageId');

        try {
            $this->dispatch(new RemoveImageFromTagCommand(
                $tagId,
                $imageId
            ));

            $this->flashSuccess('Success removing image.');
        } catch (KommerceException $e) {
            $this->flashError('Unable to remove image!');
        }

        return redirect()->route(
            'admin.tag.images',
            [
                'tagId' => $tagId,
            ]
        );
    }
}
