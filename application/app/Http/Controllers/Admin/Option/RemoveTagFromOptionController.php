<?php
namespace App\Http\Controllers\Admin\Option;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Tag\RemoveOptionFromTagCommand;
use inklabs\kommerce\Exception\KommerceException;

class RemoveTagFromOptionController extends Controller
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

            $this->flashSuccess('Successfully removed tag');
        } catch (KommerceException $e) {
            $this->flashError('Unable to remove tag');
        }

        return redirect()->route(
            'admin.option.tags',
            [
                'optionId' => $optionId,
            ]
        );
    }
}
