<?php
namespace App\Http\Controllers\Admin\Option;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Option\DeleteOptionCommand;
use inklabs\kommerce\Exception\KommerceException;

class DeleteOptionController extends Controller
{
    public function post(Request $request)
    {
        $optionId = $request->input('optionId');

        try {
            $this->dispatch(new DeleteOptionCommand($optionId));
            $this->flashSuccess('Success removing option');
        } catch (KommerceException $e) {
            $this->flashError('Unable remove option.');
        }

        return redirect()->route('admin.option');
    }
}
