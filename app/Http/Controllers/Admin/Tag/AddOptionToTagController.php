<?php
namespace App\Http\Controllers\Admin\Tag;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Tag\AddOptionToTagCommand;
use inklabs\kommerce\Exception\KommerceException;

class AddOptionToTagController extends Controller
{
    public function post(Request $request)
    {
        $tagId = $request->input('tagId');
        $optionId = $request->input('optionId');

        try {
            $this->dispatch(new AddOptionToTagCommand(
                $tagId,
                $optionId
            ));
            $this->flashSuccess('Option has been added.');
        } catch (KommerceException $e) {
            $this->flashError('Unable to add option.');
        }

        return redirect()->route('admin.tag.options', ['tagId' => $tagId]);
    }
}
