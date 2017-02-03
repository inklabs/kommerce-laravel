<?php
namespace App\Http\Controllers\Admin\Tag;

use App\Http\Controllers\Controller;
use App\Lib\Arr;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Image\CreateImageForTagCommand;
use inklabs\kommerce\EntityDTO\UploadFileDTO;
use inklabs\kommerce\Exception\KommerceException;

class UploadTagImageController extends Controller
{
    public function post(Request $request)
    {
        $tagId = $request->input('tagId');
        $tag = $this->getTagWithAllData($tagId);

        $image = Arr::get($_FILES, 'image');

        $uploadFileDTO = new UploadFileDTO(
            Arr::get($image, 'name'),
            Arr::get($image, 'type'),
            Arr::get($image, 'tmp_name'),
            Arr::get($image, 'size')
        );

        if (! is_uploaded_file($uploadFileDTO->getFilePath())) {
            abort(400);
        }

        try {
            $this->dispatch(new CreateImageForTagCommand(
                $uploadFileDTO,
                $tag->id->getHex()
            ));
            $this->flashSuccess('Image uploaded.');
        } catch (KommerceException $e) {
            $this->flashError('Unable to upload image.');
        }
        return redirect()->route('admin.tag.images', ['tagId' => $tag->id->getHex()]);
    }
}
