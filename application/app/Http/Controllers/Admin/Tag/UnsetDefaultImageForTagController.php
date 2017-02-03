<?php
namespace App\Http\Controllers\Admin\Tag;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Tag\UnsetDefaultImageForTagCommand;
use inklabs\kommerce\Exception\KommerceException;

class UnsetDefaultImageForTagController extends Controller
{
    public function post(Request $request)
    {
        $tagId = $request->input('tagId');

        try {
            $this->dispatch(new UnsetDefaultImageForTagCommand(
                $tagId
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
