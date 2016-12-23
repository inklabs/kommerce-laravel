<?php
namespace App\Http\Controllers\Admin\Option;

use App\Http\Controllers\Controller;
use App\Lib\Arr;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Option\CreateOptionValueCommand;
use inklabs\kommerce\EntityDTO\OptionValueDTO;
use inklabs\kommerce\Exception\EntityValidatorException;

class AddOptionValueToOptionController extends Controller
{
    public function post(Request $request)
    {
        $optionId = $request->input('optionId');

        $optionValueDTO = new OptionValueDTO();
        $this->updateOptionValueDTOFromPost($optionValueDTO, $request->input('optionValue'));

        try {
            $this->dispatch(new CreateOptionValueCommand(
                $optionId,
                $optionValueDTO
            ));

            $this->flashSuccess('Successfully added option value');
        } catch (EntityValidatorException $e) {
            $this->flashError('Unable to save product!');
            $this->flashFormErrors($e->getErrors());
        }

        return redirect()->route(
            'admin.option.values',
            [
                'optionId' => $optionId,
            ]
        );
    }

    private function updateOptionValueDTOFromPost(OptionValueDTO & $optionValueDTO, array $optionValue)
    {
        $unitPrice = (int) floor(Arr::get($optionValue, 'unitPrice') * 100);

        $optionValueDTO->name = trim(Arr::get($optionValue, 'name'));
        $optionValueDTO->sku = trim(Arr::get($optionValue, 'sku'));
        $optionValueDTO->unitPrice = $unitPrice;
        $optionValueDTO->shippingWeight = Arr::get($optionValue, 'shippingWeight');
        $optionValueDTO->sortOrder = Arr::get($optionValue, 'sortOrder');
    }
}
