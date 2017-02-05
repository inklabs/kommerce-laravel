<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\User\GetUserByEmailQuery;
use inklabs\kommerce\Action\User\LoginCommand;
use inklabs\kommerce\Action\User\Query\GetUserByEmailRequest;
use inklabs\kommerce\Action\User\Query\GetUserByEmailResponse;
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

            // TODO: This operation must be in the application context, not the user session context
            $request = new GetUserByEmailRequest($email);
            $response = new GetUserByEmailResponse();
            $this->dispatchQuery(new GetUserByEmailQuery($request, $response));
            $user = $response->getUserDTOWithRolesAndTokens();
            $this->saveUserToSession($user);

            // Todo: Merge anonymous session cart with users cart, this may become a single command

            return redirect($redirect);
        } catch (UserLoginException $e) {
            $this->flashError('Invalid username or password.');
        }

        return $this->get();
    }
}
