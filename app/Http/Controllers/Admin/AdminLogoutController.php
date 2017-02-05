<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class AdminLogoutController extends Controller
{
    public function post()
    {
        $this->removeUserFromSession();
        return redirect()->route('home');
    }
}
