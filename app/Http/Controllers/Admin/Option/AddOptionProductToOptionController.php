<?php
namespace App\Http\Controllers\Admin\Option;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Option\CreateOptionProductCommand;
use inklabs\kommerce\EntityDTO\OptionProductDTO;
use inklabs\kommerce\Exception\KommerceException;

class AddOptionProductToOptionController extends Controller
{
    public function post(Request $request)
    {
        $optionId = $request->input('optionId');
        $productId = $request->input('productId');

        $optionProductDTO = new OptionProductDTO();
        $optionProductDTO->sortOrder = $request->input('sortOrder', 0);

        try {
            $this->dispatch(new CreateOptionProductCommand(
                $optionId,
                $productId,
                $optionProductDTO
            ));

            $this->flashSuccess('Successfully added option product');
        } catch (KommerceException $e) {
            $this->flashError('Unable to add option product');
        }

        return redirect()->route(
            'admin.option.option-products',
            [
                'optionId' => $optionId,
            ]
        );
    }
}
