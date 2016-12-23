<?php
namespace App\Http\Controllers\Admin\Option;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Option\DeleteOptionProductCommand;
use inklabs\kommerce\Exception\KommerceException;

class DeleteOptionProductController extends Controller
{
    public function post(Request $request)
    {
        $optionId = $request->input('optionId');
        $optionProductId = $request->input('optionProductId');

        try {
            $this->dispatch(new DeleteOptionProductCommand($optionProductId));

            $this->flashSuccess('Successfully deleted option product');
        } catch (KommerceException $e) {
            $this->flashError('Unable to delete option product');
        }

        return redirect()->route(
            'admin.option.option-products',
            [
                'optionId' => $optionId,
            ]
        );
    }
}
