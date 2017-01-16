<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\User\ResetPasswordCommand;
use inklabs\kommerce\Exception\EntityNotFoundException;

class UserForgotPasswordController extends Controller
{
    public function complete()
    {
        return $this->renderTemplate('@theme/user/forgot-password-complete.twig');
    }

    public function get(Request $request)
    {
        $email = $request->query('email');
        return $this->renderWithCaptcha($email);
    }

    public function post(Request $request)
    {
        $email = $request->input('email');
        $captcha = $request->input('c');

        if ($captcha === $this->getCaptchaPhraseFromSession()) {
            try {
                $this->dispatch(new ResetPasswordCommand(
                    $email,
                    $this->getRemoteIP4(),
                    $this->getUserAgent()
                ));
            } catch (EntityNotFoundException $e) {
                // Ignore user not found
                // We don't want to expose our user email addresses
                // Todo: Log the error
            } catch (Exception $e) {
                $this->flashGenericWarning();
                return redirect()->route('user.login');
            }

            return redirect()->route('user.forgot-password.complete');
        } else {
            $this->flashTemplateError('@theme/flash/invalid-captcha.twig');
        }

        return $this->renderWithCaptcha($email);
    }

    private function renderWithCaptcha($email = '')
    {
        return $this->renderTemplate(
            '@theme/user/forgot-password.twig',
            [
                'email' => $email,
                'captchaInlineSrc' => $this->getCaptchaBuilder()->inline(),
            ]
        );
    }
}
