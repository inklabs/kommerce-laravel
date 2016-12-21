<?php
namespace App\Http\Controllers\Admin\Tag;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Tag\RemoveOptionFromTagCommand;
use inklabs\kommerce\Exception\KommerceException;

class RemoveOptionFromTagController extends Controller
{
    public function post(Request $request)
    {
        $tagId = $request->input('tagId');
        $optionId = $request->input('optionId');

        try {
            $this->dispatch(new RemoveOptionFromTagCommand(
                $tagId,
                $optionId
            ));

            $this->flashSuccess('Success removing option.');
        } catch (KommerceException $e) {
            $this->flashError('Unable to remove option!');
        }

        return redirect()->route(
            'admin.tag.options',
            [
                'tagId' => $tagId,
            ]
        );
    }
}
