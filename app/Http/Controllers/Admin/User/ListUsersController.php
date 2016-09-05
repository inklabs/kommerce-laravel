<?php
namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ListUsersController extends Controller
{
    public function index(Request $httpRequest)
    {
        $users = [];
        $pagination = null;

        return $this->renderTemplate(
            'admin/user/index.twig',
            [
                'users' => $users,
                'pagination' => $pagination,
            ]
        );
    }
}
