<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\User\ChangePasswordCommand;
use inklabs\kommerce\Exception\KommerceException;
use inklabs\kommerce\Exception\UserPasswordValidationException;
use inklabs\kommerce\tests\Helper\Entity\DummyData;

class ChangePasswordController extends Controller
{
    public function index()
    {
        $dummyData = new DummyData();
        $user = $this->getDTOBuilderFactory()->getUserDTOBuilder($dummyData->getUser())->build();

        return $this->renderTemplate(
            '@theme/user/change-password.twig'
        );
    }

    public function post(Request $request)
    {
        $passwordNew = $request->input('passwordNew');
        $passwordCheck = $request->input('passwordCheck');

        if ($passwordNew !== $passwordCheck) {
            $this->flashError('Please check that your passwords match and try again.');
            return redirect()->route('user.change-password');
        }

        $user = $this->getUserFromSession();

        try {
            $this->dispatch(new ChangePasswordCommand(
                $user->id,
                $passwordNew
            ));
            $this->flashSuccess('Password changed');
        } catch (UserPasswordValidationException $e) {
            $this->flashError($e->getMessage());
            return redirect()->route('user.change-password');
        } catch (KommerceException $e) {
            $this->flashGenericWarning();
            return redirect()->route('user.account');
        }

        return redirect()->route("user.account");
    }
}
