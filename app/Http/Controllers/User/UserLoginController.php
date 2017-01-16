<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserLoginController extends Controller
{
    public function get()
    {
        return $this->renderTemplate('@theme/user/login.twig');
    }

    public function post(Request $request)
    {
        dd($request->input());
        // TODO: Finish user login
    }
}
