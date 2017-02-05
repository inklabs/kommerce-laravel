<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserLogoutController extends Controller
{
    public function post(Request $request)
    {
        $this->removeUserFromSession();
        return redirect()->route('home');
    }
}
