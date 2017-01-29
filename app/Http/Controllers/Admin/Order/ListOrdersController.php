<?php
namespace App\Http\Controllers\Admin\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Order\ListOrdersQuery;
use inklabs\kommerce\Action\Order\Query\ListOrdersRequest;
use inklabs\kommerce\Action\Order\Query\ListOrdersResponse;

class ListOrdersController extends Controller
{
    public function index(Request $httpRequest)
    {
        $queryString = $httpRequest->query('q');

        $request = new ListOrdersRequest(
            $queryString, // TODO: Implement in kommerce-core
            $this->getPaginationDTO(20)
        );

        $response = new ListOrdersResponse();
        $this->dispatchQuery(new ListOrdersQuery($request, $response));

        return $this->renderTemplate(
            '@admin/order/index.twig',
            [
                'orders' => $response->getOrderWithUserDTOs(),
                'pagination' => $response->getPaginationDTO(),
                'queryString' => $queryString,
            ]
        );
    }
}
