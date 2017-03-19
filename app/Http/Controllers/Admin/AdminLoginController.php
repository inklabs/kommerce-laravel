<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\User\GetUserByEmailQuery;
use inklabs\kommerce\Action\User\LoginCommand;
use inklabs\kommerce\ActionResponse\User\GetUserByEmailResponse;
use inklabs\kommerce\Exception\UserLoginException;

class AdminLoginController extends Controller
{
    public function get()
    {
        return $this->renderTemplate('@admin/login.twig');
    }

    public function post(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');
        $redirect = $request->input('redirect', route('admin.order'));

        try {
            $this->dispatch(new LoginCommand(
                $email,
                $password,
                $this->getRemoteIP4()
            ));

            /** @var GetUserByEmailResponse $response */
            $response = $this->adminDispatchQuery(new GetUserByEmailQuery($email));
            $user = $response->getUserDTOWithRolesAndTokens();
            $this->saveUserToSession($user);
            $this->mergeCart($user->id);

            return redirect($redirect);
        } catch (UserLoginException $e) {
            $this->flashError('Invalid username or password.');
        }

        return $this->get();
    }
}
