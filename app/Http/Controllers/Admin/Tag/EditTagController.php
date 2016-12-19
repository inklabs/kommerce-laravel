<?php
namespace App\Http\Controllers\Admin\Tag;

use App\Http\Controllers\Controller;
use App\Lib\Arr;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Tag\UpdateTagCommand;
use inklabs\kommerce\EntityDTO\TagDTO;
use inklabs\kommerce\Exception\EntityValidatorException;

class EditTagController extends Controller
{
    public function get($tagId)
    {
        $tag = $this->getTagWithAllData($tagId);

        return $this->renderTemplate(
            'admin/tag/edit.twig',
            [
                'tag' => $tag,
            ]
        );
    }

    public function post(Request $request)
    {
        $tagId = $request->input('tagId');
        $tag = $this->getTag($tagId);

        $this->updateTagDTOFromPost($tag, $request->input('tag'));

        try {
            $this->dispatch(new UpdateTagCommand($tag));

            $this->flashSuccess('Tag has been saved.');
            return redirect()->route(
                'admin.tag.edit',
                [
                    'tagId' => $tagId,
                ]
            );
        } catch (EntityValidatorException $e) {
            $this->flashError('Unable to save tag!');
            $this->flashFormErrors($e->getErrors());
        }
    }

    private function updateTagDTOFromPost(TagDTO & $tagDTO, array $tagValues)
    {
        $tagDTO->name = trim(Arr::get($tagValues, 'name'));
        $tagDTO->code = trim(Arr::get($tagValues, 'code'));
        $tagDTO->description = trim(Arr::get($tagValues, 'description'));
        $tagDTO->isActive = (bool) Arr::get($tagValues, 'isActive', false);
        $tagDTO->isVisible = (bool) Arr::get($tagValues, 'isVisible', false);
        $tagDTO->areAttachmentsEnabled = (bool) Arr::get($tagValues, 'areAttachmentsEnabled', false);
        $tagDTO->sortOrder = Arr::get($tagValues, 'sortOrder', 0);
    }
}
