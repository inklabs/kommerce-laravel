<?php
namespace App\Http\Controllers\Admin\Tag;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Tag\ListTagsQuery;
use inklabs\kommerce\Action\Tag\Query\ListTagsRequest;
use inklabs\kommerce\Action\Tag\Query\ListTagsResponse;

class ListTagsController extends Controller
{
    public function index(Request $httpRequest)
    {
        $request = new ListTagsRequest(
            $httpRequest->query('q'),
            $this->getPaginationDTO(20)
        );

        $response = new ListTagsResponse();
        $this->dispatchQuery(new ListTagsQuery($request, $response));

        $tags = $response->getTagDTOs();
        $pagination = $response->getPaginationDTO();

        return $this->renderTemplate(
            'admin/tag/index.twig',
            [
                'tags' => $tags,
                'pagination' => $pagination,
            ]
        );
    }
}
