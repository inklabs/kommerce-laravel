<?php
namespace App\Http\Controllers\Admin\Attribute;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Attribute\ListAttributesQuery;
use inklabs\kommerce\Action\Attribute\Query\ListAttributesRequest;
use inklabs\kommerce\Action\Attribute\Query\ListAttributesResponse;

class ListAttributesController extends Controller
{
    public function index(Request $httpRequest)
    {
        $queryString = $httpRequest->query('q');

        $request = new ListAttributesRequest(
            $queryString,
            $this->getPaginationDTO(20)
        );

        $response = new ListAttributesResponse();
        $this->dispatchQuery(new ListAttributesQuery($request, $response));

        $attributes = $response->getAttributeDTOs();
        $pagination = $response->getPaginationDTO();

        return $this->renderTemplate(
            '@theme/admin/attribute/index.twig',
            [
                'attributes' => $attributes,
                'pagination' => $pagination,
                'queryString' => $queryString,
            ]
        );
    }
}
