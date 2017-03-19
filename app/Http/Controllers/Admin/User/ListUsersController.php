<?php
namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\User\ListUsersQuery;
use inklabs\kommerce\ActionResponse\User\ListUsersResponse;

class ListUsersController extends Controller
{
    public function index(Request $httpRequest)
    {
        $queryString = $httpRequest->query('q');

        /** @var ListUsersResponse $response */
        $response = $this->dispatchQuery(new ListUsersQuery(
            $queryString,
            $this->getPaginationDTO(20)
        ));

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
