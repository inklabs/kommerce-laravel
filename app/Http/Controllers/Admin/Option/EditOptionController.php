<?php
namespace App\Http\Controllers\Admin\Option;

use App\Http\Controllers\Controller;
use App\Lib\Arr;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Option\UpdateOptionCommand;
use inklabs\kommerce\Entity\OptionType;
use inklabs\kommerce\Exception\EntityValidatorException;

class EditOptionController extends Controller
{
    public function get($optionId)
    {
        $option = $this->getOptionWithAllData($optionId);

        return $this->renderTemplate(
            '@admin/option/edit.twig',
            [
                'option' => $option,
                'optionTypes' => OptionType::getSlugNameMap(),
            ]
        );
    }

    public function post(Request $request)
    {
        $optionId = $request->input('optionId');
        $optionValues = $request->input('option');
        $name = trim(Arr::get($optionValues, 'name'));
        $description = trim(Arr::get($optionValues, 'description'));
        $sortOrder = trim(Arr::get($optionValues, 'sortOrder', 0));
        $optionTypeSlug = Arr::get($optionValues, 'type');

        try {
            $this->dispatch(new UpdateOptionCommand(
                $name,
                $description,
                $sortOrder,
                $optionTypeSlug,
                $optionId
            ));

            $this->flashSuccess('Option has been saved.');
            return redirect()->route(
                'admin.option.edit',
                [
                    'optionId' => $optionId,
                ]
            );
        } catch (EntityValidatorException $e) {
            $this->flashError('Unable to save option!');
            $this->flashFormErrors($e->getErrors());
        }

        return $this->renderTemplate(
            '@admin/option/edit.twig',
            [
                'option' => $option,
                'optionTypes' => OptionType::getSlugNameMap(),
            ]
        );
    }
}
