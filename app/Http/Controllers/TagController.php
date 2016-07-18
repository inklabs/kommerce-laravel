<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use inklabs\kommerce\Action\Tag\CreateTagCommand;
use inklabs\kommerce\EntityDTO\TagDTO;

class TagController extends Controller
{

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required'
        ]);

        $tagDTO = new TagDTO();

        $tagDTO->slug = $request->get('slug');
        $tagDTO->sku = $request->get('sku');

        $this->dispatch(new CreateTagCommand($tagDTO));

        return redirect()->route('p.show', $tagDTO->id);
    }

}
