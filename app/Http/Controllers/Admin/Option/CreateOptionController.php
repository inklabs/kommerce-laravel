<?php
namespace App\Http\Controllers\Admin\Option;

use App\Http\Controllers\Controller;
use App\Lib\Arr;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Option\CreateOptionCommand;
use inklabs\kommerce\Entity\OptionType;
use inklabs\kommerce\EntityDTO\OptionDTO;
use inklabs\kommerce\Exception\EntityValidatorException;

class CreateOptionController extends Controller
{
    public function get()
    {
        return $this->renderTemplate(
            '@admin/option/new.twig',
            [
                'optionTypes' => OptionType::getSlugNameMap(),
            ]
        );
    }

    public function post(Request $request)
    {
        $optionValues = $request->input('option');
        $name = trim(Arr::get($optionValues, 'name'));
        $description = trim(Arr::get($optionValues, 'description'));
        $sortOrder = trim(Arr::get($optionValues, 'sortOrder', 0));
        $optionTypeSlug = Arr::get($optionValues, 'type');

        try {
            $command = new CreateOptionCommand(
                $name,
                $description,
                $sortOrder,
                $optionTypeSlug
            );
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
            '@admin/option/new.twig',
            [
                'option' => $option,
                'optionTypes' => OptionType::getSlugNameMap(),
            ]
        );
    }

    private function updateOptionDTOFromPost(OptionDTO & $optionDTO, array $optionValues)
    {
        $optionDTO->name = trim(Arr::get($optionValues, 'name'));
        $optionDTO->description = trim(Arr::get($optionValues, 'description'));
    }
}
