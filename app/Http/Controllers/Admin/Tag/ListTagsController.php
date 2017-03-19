<?php
namespace App\Http\Controllers\Admin\Tag;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Tag\ListTagsQuery;
use inklabs\kommerce\ActionResponse\Tag\ListTagsResponse;

class ListTagsController extends Controller
{
    public function index(Request $httpRequest)
    {
        $queryString = $httpRequest->query('q');

        /** @var ListTagsResponse $response */
        $response = $this->dispatchQuery(new ListTagsQuery(
            $queryString,
            $this->getPaginationDTO(20)
        ));

        $tags = $response->getTagDTOs();
        $pagination = $response->getPaginationDTO();

        return $this->renderTemplate(
            '@admin/tag/index.twig',
            [
                'tags' => $tags,
                'pagination' => $pagination,
                'queryString' => $queryString,
            ]
        );
    }
}
