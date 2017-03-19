<?php
namespace App\Http\Controllers\Admin\Attribute;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Attribute\ListAttributesQuery;
use inklabs\kommerce\ActionResponse\Attribute\ListAttributesResponse;

class ListAttributesController extends Controller
{
    public function index(Request $httpRequest)
    {
        $queryString = $httpRequest->query('q');

        /** @var ListAttributesResponse $response */
        $response = $this->dispatchQuery(new ListAttributesQuery(
            $queryString,
            $this->getPaginationDTO(20)
        ));

        $attributes = $response->getAttributeDTOs();
        $pagination = $response->getPaginationDTO();

        return $this->renderTemplate(
            '@admin/attribute/index.twig',
            [
                'attributes' => $attributes,
                'pagination' => $pagination,
                'queryString' => $queryString,
            ]
        );
    }
}
