<?php
namespace App\Http\Controllers\Admin\Option;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Option\DeleteOptionValueCommand;
use inklabs\kommerce\Exception\KommerceException;

class DeleteOptionValueController extends Controller
{
    public function post(Request $request)
    {
        $optionId = $request->input('optionId');
        $optionValueId = $request->input('optionValueId');

        try {
            $this->dispatch(new DeleteOptionValueCommand($optionValueId));

            $this->flashSuccess('Success deleting option value.');
        } catch (KommerceException $e) {
            $this->flashError('Unable to delete option value!');
        }

        return redirect()->route(
            'admin.option.values',
            [
                'optionId' => $optionId,
            ]
        );
    }
}
