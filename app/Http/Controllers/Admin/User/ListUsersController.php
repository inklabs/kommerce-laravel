<?php
namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\User\ListUsersQuery;
use inklabs\kommerce\Action\User\Query\ListUsersRequest;
use inklabs\kommerce\Action\User\Query\ListUsersResponse;

class ListUsersController extends Controller
{
    public function index(Request $httpRequest)
    {
        $queryString = $httpRequest->query('q');

        $request = new ListUsersRequest(
            $queryString,
            $this->getPaginationDTO(20)
        );

        $response = new ListUsersResponse();
        $this->dispatchQuery(new ListUsersQuery($request, $response));

        $users = $response->getUserDTOs();
        $pagination = $response->getPaginationDTO();

        return $this->renderTemplate(
            '@admin/user/index.twig',
            [
                'users' => $users,
                'pagination' => $pagination,
                'queryString' => $queryString,
            ]
        );
    }
}
