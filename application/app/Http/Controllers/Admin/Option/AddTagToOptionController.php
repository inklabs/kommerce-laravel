<?php
namespace App\Http\Controllers\Admin\Option;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Tag\AddOptionToTagCommand;
use inklabs\kommerce\Exception\KommerceException;

class AddTagToOptionController extends Controller
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

            $this->flashSuccess('Successfully added tag');
        } catch (KommerceException $e) {
            $this->flashError('Unable to add tag');
        }

        return redirect()->route(
            'admin.option.tags',
            [
                'optionId' => $optionId,
            ]
        );
    }
}
