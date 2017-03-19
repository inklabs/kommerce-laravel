<?php
namespace App\Http\Controllers\Admin\Option;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Option\ListOptionsQuery;
use inklabs\kommerce\ActionResponse\Option\ListOptionsResponse;

class ListOptionsController extends Controller
{
    public function index(Request $httpRequest)
    {
        $queryString = $httpRequest->query('q');

        /** @var ListOptionsResponse $response */
        $response = $this->dispatchQuery(new ListOptionsQuery(
            $queryString,
            $this->getPaginationDTO(20)
        ));

        $options = $response->getOptionDTOs();
        $pagination = $response->getPaginationDTO();

        return $this->renderTemplate(
            '@admin/option/index.twig',
            [
                'options' => $options,
                'pagination' => $pagination,
                'queryString' => $queryString,
            ]
        );
    }
}
