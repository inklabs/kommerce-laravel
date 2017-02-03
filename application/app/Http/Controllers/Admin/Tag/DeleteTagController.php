<?php
namespace App\Http\Controllers\Admin\Tag;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Tag\DeleteTagCommand;
use inklabs\kommerce\Exception\KommerceException;

class DeleteTagController extends Controller
{
    public function post(Request $request)
    {
        $tagId = $request->input('tagId');

        try {
            $this->dispatch(new DeleteTagCommand($tagId));
            $this->flashSuccess('Success removing tag');
        } catch (KommerceException $e) {
            $this->flashError('Unable remove tag.');
        }

        return redirect()->route('admin.tag');
    }
}
