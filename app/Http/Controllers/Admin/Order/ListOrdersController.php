<?php
namespace App\Http\Controllers\Admin\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Order\ListOrdersQuery;
use inklabs\kommerce\ActionResponse\Order\ListOrdersResponse;

class ListOrdersController extends Controller
{
    public function index(Request $httpRequest)
    {
        $queryString = $httpRequest->query('q');

        /** @var ListOrdersResponse $response */
        $response = $this->dispatchQuery(new ListOrdersQuery(
            $queryString, // TODO: Implement in kommerce-core
            $this->getPaginationDTO(20)
        ));

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
