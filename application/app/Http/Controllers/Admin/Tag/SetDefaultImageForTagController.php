<?php
namespace App\Http\Controllers\Admin\Tag;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Tag\SetDefaultImageForTagCommand;
use inklabs\kommerce\Exception\KommerceException;

class SetDefaultImageForTagController extends Controller
{
    public function post(Request $request)
    {
        $tagId = $request->input('tagId');
        $imageId = $request->input('imageId');

        try {
            $this->dispatch(new SetDefaultImageForTagCommand(
                $tagId,
                $imageId
            ));

            $this->flashSuccess('Success updating default image.');
        } catch (KommerceException $e) {
            $this->flashError('Unable to update default image!');
        }

        return redirect()->route(
            'admin.tag.images',
            [
                'tagId' => $tagId,
            ]
        );
    }
}
