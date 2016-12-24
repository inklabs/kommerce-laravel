<?php
namespace App\Http\Controllers\Admin\Option;

use App\Http\Controllers\Controller;
use App\Lib\Arr;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Option\CreateOptionCommand;
use inklabs\kommerce\Action\Option\UpdateOptionCommand;
use inklabs\kommerce\EntityDTO\OptionDTO;
use inklabs\kommerce\Exception\EntityValidatorException;

class EditOptionController extends Controller
{
    public function getNew()
    {
        return $this->renderTemplate('@theme/admin/option/new.twig');
    }

    public function postNew(Request $request)
    {
        $option = new OptionDTO();
        $this->updateOptionDTOFromPost($option, $request->input('option'));

        try {
            $command = new CreateOptionCommand($option);
            $this->dispatch($command);

            $this->flashSuccess('Option has been created.');
            return redirect()->route(
                'admin.option.edit',
                [
                    'optionId' => $command->getOptionId()->getHex(),
                ]
            );
        } catch (EntityValidatorException $e) {
            $this->flashError('Unable to create option!');
            $this->flashFormErrors($e->getErrors());
        }

        return $this->renderTemplate(
            '@theme/admin/option/new.twig',
            [
                'option' => $option,
            ]
        );
    }

    public function getEdit($optionId)
    {
        $option = $this->getOptionWithAllData($optionId);

        return $this->renderTemplate(
            '@theme/admin/option/edit.twig',
            [
                'option' => $option,
            ]
        );
    }

    public function postEdit(Request $request)
    {
        $optionId = $request->input('optionId');
        $option = $this->getOptionWithAllData($optionId);

        $this->updateOptionDTOFromPost($option, $request->input('option'));

        try {
            $this->dispatch(new UpdateOptionCommand($option));

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
            '@theme/admin/option/edit.twig',
            [
                'option' => $option,
            ]
        );
    }

    private function updateOptionDTOFromPost(OptionDTO & $optionDTO, array $optionValues)
    {
        $optionDTO->name = trim(Arr::get($optionValues, 'name'));
        $optionDTO->description = trim(Arr::get($optionValues, 'description'));
    }
}
